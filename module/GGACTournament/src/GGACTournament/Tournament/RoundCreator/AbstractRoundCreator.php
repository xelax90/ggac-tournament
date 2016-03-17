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

namespace GGACTournament\Tournament\RoundCreator;

use DoctrineModule\Persistence\ProvidesObjectManager;
use GGACTournament\Tournament\Teamdata\Manager as TeamdataManager;
use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use GGACTournament\Entity\Game;
use GGACTournament\Entity\Match;
use GGACTournament\Entity\Group;
use GGACTournament\Entity\Round;
use GGACTournament\Entity\Team;

/**
 * Provides some standard round creator functionality
 *
 * @author schurix
 */
abstract class AbstractRoundCreator implements RoundCreatorInterface{
	use ProvidesObjectManager;
	
	/** @var TeamdataManager */
	protected $teamdataManager;
	
	/** @var ApiDataManager */
	protected $apiDataManager;
	
	/**
	 * @{inheritDoc}
	 */
	public function getTeamdataManager() {
		return $this->teamdataManager;
	}

	/**
	 * @{inheritDoc}
	 */
	public function setTeamdataManager(TeamdataManager $teamdataManager) {
		$this->teamdataManager = $teamdataManager;
		return $this;
	}
	
	/**
	 * @{inheritDoc}
	 */
	public function getApiDataManager() {
		return $this->apiDataManager;
	}

	/**
	 * @{inheritDoc}
	 */
	public function setApiDataManager(ApiDataManager $apiDataManager) {
		$this->apiDataManager = $apiDataManager;
		return $this;
	}

	/**
	 * @{inheritDoc}
	 */
	public function getDefaultConfig() {
		return new RoundConfig($this->_getDefaultConfig());
	}
	
	/**
	 * Returns array of default round configuration options that 
	 * will be merged with the standard round config
	 * @return array
	 */
	abstract protected function _getDefaultConfig();
	
	/**
	 * Creates games for the passed match according to it's round's configuration
	 * @param Match $match
	 */
	protected function createGamesForMatch(Match $match){
		$gamesPerMatch = $match->getRound()->getGamesPerMatch();
		
		$games = array();
		for($j = 0; $j < $gamesPerMatch; $j++){
			$game = new Game();
			if($j % 2 == 0){
				$teamBlue = $match->getTeamHome();
				$teamPurple = $match->getTeamGuest();
			} else {
				$teamBlue = $match->getTeamGuest();
				$teamPurple = $match->getTeamHome();
			}
			$game->setTeamBlue($teamBlue);
			$game->setTeamPurple($teamPurple);
			$game->setNumber($j+1);
			$game->setMatch($match);
			$games[] = $game;
		}
		$match->setGames($games);
	}
	
	/**
	 * Creates a round for passed group with given config
	 * @param Group $group
	 * @param RoundConfig $roundConfig
	 * @return Round
	 */
	protected function createRound(Group $group, RoundConfig $roundConfig){
		$round = new Round();
		$round->setNumber($group->getMaxRoundNumber() + 1);
		$round->setGroup($group);
		$round->setType(get_class($this));
		$round->setGamesPerMatch($roundConfig->getGamesPerMatch());
		$round->setPointsPerGamePoint($roundConfig->getPointsPerGamePoint());
		$round->setPointsPerMatchWin($roundConfig->getPointsPerMatchWin());
		$round->setPointsPerMatchDraw($roundConfig->getPointsPerMatchDraw());
		$round->setPointsPerMatchLoss($roundConfig->getPointsPerMatchLoss());
		$round->setPointsPerMatchFree($roundConfig->getPointsPerMatchFree());
		$round->setIgnoreColors($roundConfig->getIgnoreColors());
		$round->setStartDate($roundConfig->getStartDate());
		$round->setDuration($roundConfig->getDuration());
		$round->setTimeForDates($roundConfig->getTimeForDates());
		$round->setIsHidden($roundConfig->getIsHidden());
		return $round;
	}
	
	/**
	 * Creates Match with passed arguments
	 * @param Round $round
	 * @param int $number
	 * @param Team $teamHome
	 * @param Team $teamGuest
	 * @return Match
	 */
	protected function createMatch(Round $round, $number, Team $teamHome = null, Team $teamGuest = null){
		$match = new Match();
		$match->setNumber($number);
		$match->setRound($round);
		$match->setTeamHome($teamHome);
		$match->setTeamGuest($teamGuest);
		$this->createGamesForMatch($match);
		if ($teamHome === null || $teamGuest === null) {
			$match->setIsBlocked(true);
		}
		return $match;
	}
}
