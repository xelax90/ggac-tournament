<?php

/*
 * Copyright (C) 2016 schurix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace GGACTournament\Tournament;

use Zend\Authentication\AuthenticationService;
use GGACTournament\Entity\Team;
use GGACTournament\Entity\Tournament;
use SkelletonApplication\Entity\User;
use SkelletonApplication\Entity\Role;
use GGACTournament\Entity\Player;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;

/**
 * Description of Acl
 *
 * @author schurix
 */
class Acl implements ObjectManagerAwareInterface{
	use ProvidesObjectManager;
	
	protected $guestRole = 'guest';
	protected $moderatorRole = 'moderator';
	protected $adminRole = 'administrator';
	
	
	/** @var AuthenticationService */
	protected $authenticationService;
	
	protected $ressources = array();
	
	public function getAuthenticationService() {
		return $this->authenticationService;
	}

	public function setAuthenticationService(AuthenticationService $authenticationService) {
		$this->authenticationService = $authenticationService;
		return $this;
	}

	/**
	 * 
	 * @param User $user
	 * @param Team|Tournament $team
	 */
	protected function sameTournament($user, $team){
		if(empty($user) || empty($team)){
			return false;
		}
		if($team instanceof Tournament){
			$tournament = $team;
		} else {
			$tournament = $team->getTournament();
		}
		$player = $this->getObjectManager()->getRepository(Player::class)->getPlayerForUser($user, $tournament);
		return !empty($player);
	}
	
	/**
	 * Checks if a user has the given role.
	 * @param User $user
	 * @param string $search
	 * @return boolean
	 */
	protected function userHasRole($user, $search){
		if(empty($user)){
			return $search == $this->guestRole;
		}
		$roles = $user->getRoles();
		foreach($roles as $role){
			if($this->dfsRole($role, $search)){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Checks if $role matches $search or if $search is a parent of $role;
	 * @param Role $role
	 * @param string $search
	 * @return boolean
	 */
	protected function dfsRole(Role $role, $search){
		if($role->getRoleId() == $search){
			return true;
		} elseif(!empty($role->getParent())){
			return $this->dfsRole($role->getParent(), $search);
		}
		return false;
	}
	
	/**
	 * Creates basic ressources
	 */
	public function __construct(){
		$that = $this;
		$this->addRessource('viewCaptain', function($user, $team) use($that){
			if(!$that->sameTournament($user, $team)){
				return false;
			}
			return !$that->userHasRole($user, $this->guestRole);
		});
		
		$this->addRessource('viewContacts', function($user, $team) use($that){
			if($that->userHasRole($user, $this->adminRole)){
				return true;
			}
			
			if(empty($team) || !$team instanceof Team){
				return false;
			}
			
			if($that->userHasRole($user, $this->moderatorRole) && $team->getAnsprechpartner() == $user){
				return true;
			}
			
			if($that->userHasRole($user, $this->guestRole)){
				return false;
			}
			
			if(!$that->sameTournament($user, $team)){
				return false;
			}
			
			$player = $this->getObjectManager()->getRepository(Player::class)->getPlayerForUser($user, $team->getTournament());
			return $player[0]->getTeam() == $team;
		});
		
		$this->addRessource('viewSubContacts', function($user, $team) use($that){
			if($that->userHasRole($user, $this->moderatorRole)){
				return true;
			}
			
			if($that->userHasRole($user, $this->guestRole)){
				return false;
			}
			
			return $that->sameTournament($user, $team);
		});
		
		$this->addRessource('viewAnsprechpartner', function($user, $team) use($that){
			return $that->userHasRole($user, $this->moderatorRole);
		});
		
		$this->addRessource('edit', function($user, $team) use($that){
			if($that->userHasRole($user, $this->adminRole)){
				return true;
			}
			
			if(empty($team) || !$team instanceof Team){
				return false;
			}
			
			return $that->userHasRole($user, $this->moderatorRole) && $team->getAnsprechpartner() == $user;
		});
	}
	
	/**
	 * Check if current user is allowed to access $ressource for $team
	 * @param string $ressource
	 * @param Team|Tournament $team Team to check for. If empty, returns access for all teams.
	 * @return boolean
	 */
	public function isAllowed($ressource, $team = null) {
		if(empty($this->ressources[$ressource])){
			return false;
		}
		
		$identity = $this->getAuthenticationService()->getIdentity();
		
		return call_user_func($this->ressources[$ressource], $identity, $team);
	}
	
	/**
	 * Adds a new ressource
	 * 
	 * @param string $ressource Name of ressource
	 * @param callable $check Function with 2 arguments: The user and the team or tournament. Returns true if allowed, false otherwise. Both can be null. If team is null, give result for all teams.
	 */
	public function addRessource($ressource, callable $check ){
		$this->ressources[$ressource] = $check;
	}
	
	/**
	 * Removes ressource
	 * 
	 * @param string $ressource Name of ressource
	 */
	public function removeRessource($ressource){
		if(isset($this->ressources[$ressource])){
			unset($this->ressources[$ressource]);
		}
	}
}
