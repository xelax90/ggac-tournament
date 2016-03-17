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

namespace GGACTournament\Tournament\Registration;

use GGACTournament\Tournament\AbstractManager;
use GGACTournament\Form\RegistrationSingleForm;
use GGACTournament\Form\RegistrationTeamForm;
use SkelletonApplication\Entity\User;
use GGACTournament\Entity\Player;
use GGACTournament\Entity\Registration;
use Zend\Form\FormElementManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

/**
 * Description of Manager
 *
 * @author schurix
 */
class Manager extends AbstractManager{
	
	/** @var FormElementManager */
	protected $formManager;
	
	public function getFormManager() {
		return $this->formManager;
	}

	public function setFormManager(FormElementManager $formManager) {
		$this->formManager = $formManager;
		return $this;
	}
	
	/**
	 * @return array All registrations for the managed tournament
	 */
	public function getAll(){
		return $this->getTournamentProvider()->getTournament()->getRegistrations();
	}
	
	/**
	 * @return array Two-dimensional array with all team registrations for the managed tournament. The first dimension key is the team name
	 */
	public function getTeams(){
		$result = array();
		
		$registrations = $this->getAll();
		foreach($registrations as $registration){
			if(!$registration->getTeamName()){
				continue;
			}
			$result[$registration->getTeamName()][] = $registration;
		}
		
		return $result;
	}
	
	/**
	 * @return array All registrations for managed tournament without a team name
	 */
	public function getSingles(){
		$result = array();
		
		$registrations = $this->getAll();
		foreach($registrations as $registration){
			if($registration->getTeamName()){
				continue;
			}
			$result[] = $registration;
		}
		
		return $result;
	}
	
	/**
	 * @return array List of all icons that were not picked already in the managed tournament
	 */
	public function getAvailableIcons(){
		$files = scandir($this->getTournamentOptions()->getTeamIconDirectory());
		$icons = array();
		foreach($files as $file){
			if(in_array(pathinfo($file, PATHINFO_EXTENSION), array('jpg', 'png'))){
				$icons[] = $file;
			}
		}
		
		$used = array();
		foreach($this->getAll() as $registration){
			if ($registration->getIcon()) {
				$used[] = $registration->getIcon();
			}
		}
		
		$tournament = $this->getTournamentProvider()->getTournament();
		foreach($tournament->getPhases() as $phase){
			/* @var $phase \GGACTournament\Entity\TournamentPhase */
			foreach($phase->getGroups() as $group){
				/* @var $group GroupEntity */
				foreach($group->getTeams() as $team){
					$used[] = $team->getIcon();
				}
			}
		}
		
		return array_diff($icons, $used);
	}
	
	public function dataIsTeam($data){
		return isset($data['teamName']);
	}
	
	public function dataIsSingle($data){
		return isset($data['registration']);
	}
	
	/**
	 * @return RegistrationTeamForm
	 */
	public function getTeamForm(){
		$tournament = $this->getTournamentProvider()->getTournament();
		$form = $this->getFormManager()->get(RegistrationTeamForm::class, array(
			'min_rwth' => $tournament->getRegistrationTeamMinRWTH(),
			'max_not_rwth' => $tournament->getRegistrationTeamMaxNotRWTH(),
		));
		return $form;
	}
	
	/**
	 * @return RegistrationSingleForm
	 */
	public function getSingleForm(){
		$tournament = $this->getTournamentProvider()->getTournament();
		$form = $this->getFormManager()->get(RegistrationSingleForm::class, array(
			'require_rwth' => $tournament->getRegistrationSingleRequireRWTH(),
		));
		return $form;
	}
	
	public function prefillForm(User $user, $form){
		/* @var $playerRepo \GGACTournament\Model\PlayerRepository */
		$playerRepo = $this->getObjectManager()->getRepository(Player::class);
		$players = $playerRepo->getPlayersForUser($user);
		$found = null;
		foreach($players as $player){
			/* @var $player Player */
			if($found == null || $player->getRegistration()->getTournament()->getId() > $found->getRegistration()->getTournament()->getId()){
				$found = $player;
			}
		}
		if(!$found){
			return true;
		}
		
		if($form instanceof RegistrationSingleForm){
			return $this->prefillSingleForm($found, $form);
		}
		if($form instanceof RegistrationTeamForm){
			return $this->prefillTeamForm($found, $form);
		}
		return true;
	}
	
	public function prefillSingleForm(Player $player, RegistrationSingleForm $form){
		$registration = new Registration();
		$registration->setName($player->getUser()->getDisplayName())
				->setEmail($player->getUser()->getEmail())
				->setFacebook($player->getRegistration()->getFacebook())
				->setOtherContact($player->getRegistration()->getOtherContact())
				->setSummonerId($player->getRegistration()->getSummonerId())
				->setSummonerName($player->getRegistration()->getSummonerName());
		$form->bind($registration);
		return true;
	}
	
	public function prefillTeamForm(Player $player, RegistrationTeamForm $form){
		$teamData = array();
		if(!empty($player->getTeam())){
			foreach($player->getTeam()->getPlayers() as $pl){
				/* @var $pl Player */
				$teamData[] = array(
					'name' => $pl->getUser()->getDisplayName(),
					'email' => $pl->getUser()->getEmail(),
					'facebook' => $pl->getRegistration()->getFacebook(),
					'otherContact' => $pl->getRegistration()->getOtherContact(),
					'summonerId' => $pl->getRegistration()->getSummonerId(),
					'summonerName' => $pl->getRegistration()->getSummonerName()
				);
			}
		}
		$form->setData(array(
			'teamName' => $player->getTeam()->getName(),
			'registrations' => $teamData,
		));
		
		return true;
	}
	
	public function saveForm($form){
		if($form instanceof RegistrationTeamForm){
			return $this->saveTeamForm($form);
		}
		return $this->saveSingleForm($form);
	}
	
	public function saveTeamForm(RegistrationTeamForm $form){
		$em = $this->getObjectManager();
		/* @var $hydrator \Zend\Hydrator\HydratorInterface */
		$hydrator = $this->getFormManager()->getHydratorFromName(DoctrineObject::class);
		$data = $form->getData();
		$teamName = $data['teamName'];
		$teamIcon = $data['team_icon_text'];
		$registrations = $data['registrations'];
		$team = array();
		foreach ($registrations as $registration) {
			/* @var $registration Registration */
			$newRegistration = new Registration();
			$hydrator->hydrate($registration, $newRegistration);
			$newRegistration->setTournament($this->getTournamentProvider()->getTournament())
					->setTeamName($teamName)
					->setIcon($teamIcon);
			$em->persist($newRegistration);
			$team[] = $newRegistration;
		}
		$em->flush();
		return $team;
	}
	
	public function saveSingleForm(RegistrationSingleForm $form){
		$registration = $form->getData();
		if(!$registration instanceof Registration){
			return false;
		}
		$registration->setTournament($this->getTournamentProvider()->getTournament());
		$em = $this->getObjectManager();
		$em->persist($registration);
		$em->flush();
		return $registration;
	}
}
