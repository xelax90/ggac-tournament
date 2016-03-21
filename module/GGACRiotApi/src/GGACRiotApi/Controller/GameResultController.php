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

namespace GGACRiotApi\Controller;

use GGACTournament\Controller\AbstractTournamentController;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use GGACTournament\Entity\Game;

/**
 * Description of GameResultController
 *
 * @author schurix
 */
class GameResultController extends AbstractRestfulController implements ObjectManagerAwareInterface{
	use ProvidesObjectManager;
	
	public function onDispatch(\Zend\Mvc\MvcEvent $e) {
		$return = parent::onDispatch($e);
		if(is_array($return)){
			$result = new JsonModel($return);
			$e->setResult($result);
			return $result;
		}
		return $return;
	}
	
	public function create($data) {
		$meta = json_decode($data['metaData']);
		if(!$meta || !isset($meta->game_id)){
			return array('success' => false, 'error' => 'No meta');
		}
		
		$em = $this->getObjectManager();
		/* @var $game Game */
		$game = $em->getRepository(Game::class)->find((int) $meta->game_id);
		if(!$game){
			return array('success' => false, 'error' => 'Game not found');
		}
		
		// always save report
		$game->setReport(json_encode($data));
		$em->flush($game);
		
		$blueIds = array();
		$blueNames = array();
		$purpleIds = array();
		$purpleNames = array();
		
		foreach($game->getTeamBlue()->getPlayers() as $player){ /* @var $player \GGACTournament\Entity\Player */
			$blueIds[] = $player->getRegistration()->getSummonerId();
			$blueNames[] = $player->getRegistration()->getSummonerName();
		}
		foreach($game->getTeamPurple()->getPlayers() as $player){ /* @var $player \GGACTournament\Entity\Player */
			$purpleIds[] = $player->getRegistration()->getSummonerId();
			$purpleNames[] = $player->getRegistration()->getSummonerName();
		}
		
		$purpleWins = false;
		$blueWins = false;
		foreach($data['winningTeam'] as $summoner){
			if(in_array($summoner['summonerId'], $blueIds)){
				$blueWins = true;
			}
			if(in_array($summoner['summonerId'], $purpleIds)){
				$purpleWins = true;
			}
		}
		foreach($data['losingTeam'] as $summoner){
			if(in_array($summoner['summonerId'], $blueIds)){
				$blueWins = false;
			}
			if(in_array($summoner['summonerId'], $purpleIds)){
				$purpleWins = false;
			}
		}
		
		if($purpleWins === $blueWins){
			return array('success' => false, 'error' => 'Both teams '.($purpleWins ? 'won' : 'lost'));
		}
		
		if($purpleWins){
			$game->setPointsPurple(1)
					->setPointsBlue(0);
		} else {
			$game->setPointsPurple(0)
					->setPointsBlue(1);
		}
		$em->flush();
		return array('success' => true);
	}
}
