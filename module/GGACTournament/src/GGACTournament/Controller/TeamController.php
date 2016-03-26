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

namespace GGACTournament\Controller;

use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use Zend\View\Model\ViewModel;
use GGACTournament\Entity\Player;
use GGACTournament\Entity\Team;

/**
 * Description of TeamController
 *
 * @author schurix
 */
class TeamController extends AbstractTournamentController{
	/** @var ApiDataManager */
	protected $apiDataManager;
	
	protected $player;

	public function getApiDataManager() {
		return $this->apiDataManager;
	}

	public function setApiDataManager(ApiDataManager $apiDataManager) {
		$this->apiDataManager = $apiDataManager;
		return $this;
	}

	/**
	 * @return Team
	 */
	protected function getTeam(){
		$player = $this->getPlayer();
		if(!$player){
			return null;
		}
		return $player->getTeam();
	}
	
	/**
	 * @return Player
	 */
	protected function getPlayer(){
		if(null === $this->player){
			$tournament = $this->getTournamentProvider()->getTournament();
			if(!$tournament){
				return $this->player;
			}
			if(!$this->zfcUserAuthentication()->hasIdentity()){
				return $this->player;
			}
			// get user
			$user = $this->zfcUserAuthentication()->getIdentity();
			$em = $this->getObjectManager();
			/* @var $playerRepo \GGACTournament\Model\PlayerRepository */
			$playerRepo = $em->getRepository(Player::class);
			// get current tournament player
			$players = $playerRepo->getPlayerForUser($user, $tournament);
			if(!$players || count($players) == 0){
				return $this->player;
			}
			$player = $players[0];
			$this->player = $player;
		}
		return $this->player;
	}

	public function teamsAction(){
		$this->getApiDataManager()->setData();
		$tournament = $this->getTournamentProvider()->getTournament();
		
		return new ViewModel(array(
			'loginForm' => $this->getLoginForm(),
			'tournament' => $tournament,
		));
	}

	public function myTeamAction(){
		$this->getApiDataManager()->setData();
		$tournament = $this->getTournamentProvider()->getTournament();
		$team = $this->getTeam();
		
		return new ViewModel(array(
			'loginForm' => $this->getLoginForm(),
			'tournament' => $tournament,
			'team' => $team,
		));
	}
}
