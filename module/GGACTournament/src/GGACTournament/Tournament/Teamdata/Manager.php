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

namespace GGACTournament\Tournament\Teamdata;

use GGACTournament\Entity\Match;
use GGACTournament\Entity\Game;
use GGACTournament\Entity\Group;
use GGACTournament\Entity\Team;
use GGACTournament\Entity\Round;

use GGACTournament\Tournament\AbstractManager;
use Doctrine\Common\Collections\Collection;

/**
 * Manages team data (Scores, Feinwertungen, etc.)
 *
 * @author schurix
 */
class Manager extends AbstractManager{
	
	/** @var Cache */
	protected $cache;
	
	/** @var array */
	protected $teamdata;
	
	/**
	 * @return Cache
	 */
	public function getCache() {
		return $this->cache;
	}
	
	/**
	 * @param Cache $cache
	 * @return Manager
	 */
	public function setCache(Cache $cache) {
		$this->cache = $cache;
		return $this;
	}

	/**
	 * Returns teamdata given group. Returns array with group IDs as key when $group is null. 
	 * Tries to use cache if $refresh not set to true
	 * @param Group $group
	 * @param boolean $refresh
	 * @return array
	 */
	public function getTeamdata($refresh = false){
		if(!$refresh && $this->teamdataIsComplete()){
			// Teamdata is complete and no refresh is forced
			return $this->teamdata;
		}
		
		if($this->teamdata === null){
			$this->teamdata = array();
		}
		
		// refresh all groups in all phases
		$phases = $this->getTournamentProvider()->getTournament()->getPhases();
		if($phases instanceof Collection){
			$phases = $phases->toArray();
		}
		$phases = array_reverse($phases);
		foreach($phases as $phase){
			/* @var $phase \GGACTournament\Entity\TournamentPhase */
			foreach($phase->getGroups() as $group){
				/* @var $group Group */
				$this->teamdata[$group->getId()] = $this->getTeamdataPerRound($group, $refresh);
			}
		}
		return $this->teamdata;
	}
	
	/**
	 * Injects the data of a round into teams
	 * @param Group $group Pass null if you want to inject into all groups
	 * @param Round $round Pass null to inject the most recent round data, Pass a round inject data from this specific round
	 * @param boolean $onlyVisibleRound If not set to false, data of the most recent visible round will be injected. Otherwise hidden rounds are also taken into account
	 * @param boolean $refresh Set to true if you want to force recomputation of teamdata
	 */
	public function injectTeamdata(Group $group = null, Round $round = null, $onlyVisibleRound = true, $refresh = false){
		$teamdata = $this->getTeamdata($refresh);
		$groups = array();
		if($group){
			$groups = array($group);
		} else {
			$phase = $this->getTournamentProvider()->getTournament()->getCurrentPhase();
			/* @var $phase \GGACTournament\Entity\TournamentPhase */
			$phaseGroups = $phase->getGroups();
			if($phaseGroups instanceof Collection){
				$phaseGroups = $phaseGroups->toArray();
			}
			$groups = array_merge($groups, $phaseGroups);
		}
		foreach($groups as $group){
			/* @var $group Group */
			$groupdata = $teamdata[$group->getId()];
			if($round === null){
				$lastRound = $group->getLastRound($onlyVisibleRound);
			} else {
				$lastRound = $round;
			}
			
			$roundId = 0;
			if($lastRound){
				$roundId = $lastRound->getId();
			}
			
			foreach($group->getTeams() as $team){
				/* @var $team Team */
				if(isset($groupdata[$roundId][$team->getId()])){
					$team->setData($groupdata[$roundId][$team->getId()]);
				}
			}
		}
	}
	
	/**
	 * Checks if stored teamdata has an entry for each group in the tournament
	 * @return boolean
	 */
	protected function teamdataIsComplete(){
		if(null === $this->teamdata){
			return false;
		}
		foreach($this->getTournamentProvider()->getTournament()->getPhases() as $phase){
			/* @var $phase \GGACTournament\Entity\TournamentPhase */
			foreach($phase->getGroups() as $group){
				/* @var $group Group */
				if(!isset($this->teamdata[$group->getId()])){
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Computes the teamdata for each round in the passed group. 
	 * Tries to use cache if $refresh is not set to true.
	 * Returns a 2-dimensional array where first key is the round id and the second key is the team id.
	 * @param Group $group
	 * @param boolean $refresh
	 * @param array $baseData
	 * @return array
	 */
	protected function getTeamdataPerRound(Group $group, $refresh = false){
		if(!$refresh){ // if no refresh requested
			// try to load data from cache
			$cacheData = $this->getTeamdataFromCache($group);
			if($cacheData){
				return $cacheData;
			}
		}
		
		$phase = $group->getPhase();
		$previousPhase = $this->getTournamentProvider()->getTournament()->getPreviousPhase($phase);
		
		$rounds = $group->getRounds()->toArray();
		$teams = $group->getTeams();
		
		// sort rounds by round number (ascending)
		usort($rounds, function($r1, $r2){return $r1->getNumber() - $r2->getNumber();});
		
		$result = array();
		
		$previousRoundData = array();
		if($phase->getResetPoints() || empty($previousPhase)){
			// create empty data to start with
			foreach($teams as $team){
				$data = new Data();
				$data->setTeam($team)
						->setTiebreakOrder($group->getPhase()->getTiebreakOrder());
				$previousRoundData[$team->getId()] = $data;
			}
		} else {
			// Use points of last rount of previous phase as base
			$previousRoundData = array();
			foreach($teams as $team){
				/* @var $team Team */
				$teamGroup = $team->getGroup($previousPhase);
				$previousRoundData[$team->getId()] = end($this->teamdata[$teamGroup->getId()])[$team->getId()];
				$previousRoundData[$team->getId()]->setTiebreakOrder($group->getPhase()->getTiebreakOrder());
			}
		}
		
		$result[0] = $previousRoundData;
		
		foreach($rounds as $round){
			/* @var $round Round */
			$roundData = array();
			// copy previous round data
			foreach($teams as $team){
				$olddata = $previousRoundData[$team->getId()];
				$roundData[$team->getId()] = new Data($olddata);
			}
			// points, playedHome, playedGuest, previousGameHome, penultimateGameHome
			foreach($round->getMatches() as $match){
				$th = $match->getTeamHome();
				$tg = $match->getTeamGuest();
				
				// get match points
				$matchPoints = $this->getPointsForMatch($match);
				if ($th) {
					$roundData[$th->getId()]->setPoints($roundData[$th->getId()]->getPoints() + $matchPoints[$th->getId()]);
				}
				if ($tg) {
					$roundData[$tg->getId()]->setPoints($roundData[$tg->getId()]->getPoints() + $matchPoints[$tg->getId()]);
				}
				
				if(!$round->getIgnoreColors()){ // if color is not ignored in this round
					// calculate playedHome, previousGameHome and penultimateGameHome
					if($th){
						$roundData[$th->getId()]->setPlayedHome($roundData[$th->getId()]->getPlayedHome() + 1);
						$roundData[$th->getId()]->setPenultimateGameHome($roundData[$th->getId()]->getPreviousGameHome());
						$roundData[$th->getId()]->setPreviousGameHome(true);
					}
					
					if($tg){
						$roundData[$tg->getId()]->setPlayedGuest($roundData[$tg->getId()]->getPlayedGuest() + 1);
						$roundData[$tg->getId()]->setPenultimateGameHome($roundData[$tg->getId()]->getPreviousGameHome());
						$roundData[$tg->getId()]->setPreviousGameHome(false);
					}
				}
			}
			
			// update $previousRoundData
			$result[$round->getId()] = $roundData;
			$previousRoundData = $roundData;
		}
		
		$this->getCache()->addItem($this->getCacheKey($group), serialize($result));
		return $result;
	}

	/**
	 * Computes the cache key for the group. The key should change each time when teamdata changes
	 * @param Group $group
	 * @return string
	 */
	protected function getCacheKey(Group $group){
		$rounds = $group->getRounds();
		$maxRoundId = 0;
		foreach($rounds as $round){
			$maxRoundId = max($maxRoundId, $round->getId());
		}
		$teams = $group->getTeams();
		$maxTeamId = 0;
		foreach($teams as $team){
			$maxTeamId = max($maxTeamId, $team->getId());
		}
		return $this->getTournamentProvider()->getTournament()->getId()."_".$group->getId()."_".$maxRoundId."_".$maxTeamId;
	}
	
	/**
	 * Returns teamdata stored in cache or null if nothing stored.
	 * @param Group $group
	 * @return array|null
	 */
	protected function getTeamdataFromCache(Group $group){
		$cacheKey = $this->getCacheKey($group);
		$cache = $this->getCache();
		if(!$cache->hasItem($cacheKey) || ($cache->itemHasExpired($cacheKey) && false)){ // TODO remove && false
			// if no cache entry found or the entry expired
			return null;
		}
		$teamdata = unserialize($cache->getItem($cacheKey));
		$teams = $group->getTeams();
		foreach($teamdata as $r => $data){
			foreach($teams as $team){
				$teamdata[$r][$team->getId()]->setTeam($team);
			}
		}
		return $teamdata;
	}
	
	/**
	 * Computes the team points for the passed match taking the round properties into account.
	 * @param Match $match
	 * @return array
	 */
	protected function getPointsForMatch(Match $match){
		$round = $match->getRound();
		$th = $match->getTeamHome();
		$tg = $match->getTeamGuest();
		
		if($th == null || $tg == null){
			$team = $th ?: $tg;
			return array($team->getId() => $match->getRound()->getPointsPerMatchFree());
		}
		
		if($match->getPointsHome() !== null && $match->getPointsGuest() !== null){
			$gamesWonHome = $match->getPointsHome();
			$gamesWonGuest = $match->getPointsGuest();
			$pointsHome = $gamesWonHome * $round->getPointsPerGamePoint();
			$pointsGuest = $gamesWonGuest * $round->getPointsPerGamePoint();
		} else {
			// team points to be calculated
			$pointsHome = 0;
			$pointsGuest = 0;

			// number of won games
			$gamesWonHome = 0;
			$gamesWonGuest = 0;
			// calculate points for games
			foreach($match->getGames() as $game){
				/* @var $game Game */
				if(!$game->getPointsBlue() && !$game->getPointsPurple()){
					continue;
				}
				// Team points for blue and purple site
				$pointsBlue = $game->getPointsBlue()     * $round->getPointsPerGamePoint();
				$pointsPurple = $game->getPointsPurple() * $round->getPointsPerGamePoint();
				if($game->getTeamBlue() == $th){ // Home is blue, guest is purple
					// add points to teams
					$pointsHome  += $pointsBlue;
					$pointsGuest += $pointsPurple;
					// check which team won
					if ($game->getPointsBlue() > $game->getPointsPurple()) { // blue = home win
						$gamesWonHome++;
					} elseif ($game->getPointsPurple() > $game->getPointsBlue()) { // purple = guest win
						$gamesWonGuest++;
					}
				} else { // Home is purple, guest is blue
					$pointsHome  += $pointsPurple;
					$pointsGuest += $pointsBlue;
					if ($game->getPointsBlue() > $game->getPointsPurple()) { // blue = guest win
						$gamesWonGuest++;
					} elseif ($game->getPointsPurple() > $game->getPointsBlue()) { // purple = home win
						$gamesWonHome++;
					}
				}
			}
		}
		
		// calculate points for match
		if($pointsHome > $pointsGuest || ($pointsHome == 0 && $pointsGuest == 0 && $gamesWonHome > $gamesWonGuest)){
			// home wins
			$pointsHome  += $round->getPointsPerMatchWin();
			$pointsGuest += $round->getPointsPerMatchLoss();
		} elseif ($pointsGuest > $pointsHome || ($pointsHome == 0 && $pointsGuest == 0 && $gamesWonGuest > $gamesWonHome)) {
			// guest wins
			$pointsGuest += $round->getPointsPerMatchWin();
			$pointsHome  += $round->getPointsPerMatchLoss();
		} elseif (
			(($pointsHome != 0 || $pointsGuest != 0) && $pointsHome == $pointsGuest) || // equal points
			($pointsHome == 0 && $pointsGuest == 0 && $gamesWonHome != 0 && $gamesWonGuest != 0 && $gamesWonHome == $gamesWonGuest) // or equal number of games won
		){
			// draw
			$pointsGuest += $round->getPointsPerMatchDraw();
			$pointsHome  += $round->getPointsPerMatchDraw();
		}
		
		$res = array();
		
		if ($th) {
			$res[$th->getId()] = $pointsHome;
		}
		if ($tg) {
			$res[$tg->getId()] = $pointsGuest;
		}
		return $res;
	}
}
