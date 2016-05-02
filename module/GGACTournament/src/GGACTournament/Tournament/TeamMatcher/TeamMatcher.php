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

namespace GGACTournament\Tournament\TeamMatcher;

use GGACTournament\Tournament\AbstractManager;

use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use GGACTournament\Tournament\Registration\Manager as RegistrationManager;

use GGACTournament\Entity\Team;
use GGACTournament\Entity\Player;
use GGACTournament\Entity\Registration;

/**
 * Description of TeamMatcher
 *
 * @author schurix
 */
class TeamMatcher extends AbstractManager{
	
	/** @var ApiDataManager */
	protected $apiDataManager;
	
	/** @var RegistrationManager */
	protected $registrationManager;
	
	protected $matched;
	
	protected $teams;
	
	protected $singles;
	
	protected $subs;
	
	protected $fixed_subs = array();
	
	protected $initialized = false;
	
	protected $possible_picks = array();
	
	/**
	 * @return ApiDataManager
	 */
	public function getApiDataManager() {
		return $this->apiDataManager;
	}

	/**
	 * @param ApiDataManager $apiDataManager
	 * @return TeamMatcher
	 */
	public function setApiDataManager(ApiDataManager $apiDataManager) {
		$this->apiDataManager = $apiDataManager;
		return $this;
	}
	
	/**
	 * @return RegistrationManager
	 */
	public function getRegistrationManager() {
		return $this->registrationManager;
	}
	
	/**
	 * @param RegistrationManager $registrationManager
	 * @return TeamMatcher
	 */
	public function setRegistrationManager(RegistrationManager $registrationManager) {
		$this->registrationManager = $registrationManager;
		return $this;
	}
	
	/**
	 * Initializes teams, singles and subs array with Team and Player objects
	 */
	protected function init(){
		if($this->initialized){
			return;
		}
		
		$this->getApiDataManager()->setData();
		$singles = $this->getRegistrationManager()->getSingles();
		
		$plCount = $this->initTeams();
		$initSubRegistrations = $this->initSubs($singles);
		$plCount += count($singles) - count($this->subs);
		
		$avaliableSubs = array();
		foreach($singles as $registration){
			/* @var $registration Registration */
			if(in_array($registration->getId(), $initSubRegistrations)){
				continue;
			}
			if($registration->getIsSub() == 1){
				$avaliableSubs[] = $registration;
			}
		}
		
		$minSubs = $this->getTournamentProvider()->getTournament()->getMinimumSubs();
		$teamSize = $this->getTournamentProvider()->getTournament()->getRegistrationTeamSize();
		$missingSubs = $plCount % $teamSize; // Makes sure players can be devided by $teamSize
		if(count($this->subs) + $missingSubs < $minSubs){
			$missingSubs = $minSubs - count($this->subs);
		}
		if(count($avaliableSubs) < $missingSubs){
			$missingSubs = count($avaliableSubs);
		}
		$randomSubs = $this->randomlyChooseSubs($avaliableSubs, $missingSubs);
		
		$subRegistrations = array_merge($randomSubs, $initSubRegistrations);
		$this->initSingles($singles, $subRegistrations);
		
		$this->initMatched();
		
		$this->initialized = true;
	}
	
	/**
	 * Creates Team objects for all registrered teams and returns the number of players
	 * @return int the number of players in created teams
	 */
	protected function initTeams(){
		$this->teams = array();
		$teamSize = $this->getTournamentProvider()->getTournament()->getRegistrationTeamSize();
		$teams = $this->getRegistrationManager()->getTeams();
		
		$plCount = 0;
		foreach($teams as $teamname => $team){
			$tTeam = new Team();
			$tTeam->setName($teamname);
			$tTeam->setIcon($team[0]->getIcon());
			
			$players = array();
			foreach($team as $registration){
				/** @var \GGACTournament\Entity\Registration $registration */
				$player = $this->createPlayer($registration);
				$player->setTeam($tTeam);
				$players[] = $player;
			}
			
			$tTeam->setPlayers($players);
			
			$plCount += min([count($tTeam->getPlayers()), $teamSize]);
			
			$this->teams[] = $tTeam;
		}
		return $plCount;
	}
	
	/**
	 * Creates players for all registrations that required to be substitutes (isSub == 2)
	 * @param array $singles Array of single registrations recieved from RegistrationManager
	 * @return array List of substitutes' registration ids
	 */
	protected function initSubs($singles){
		$this->subs = array();
		$subRegistrations = array();
		foreach($singles as $registration){
			/* @var $registration \GGACTournament\Entity\Registration */
			if($registration->getIsSub() == 2 || in_array($registration->getSummonerName(), $this->fixed_subs)){
				$this->subs[] = $this->createPlayer($registration);
				$subRegistrations[] = $registration->getId();
			}
		}
		return $subRegistrations;
	}
	
	/**
	 * Randomly chooses a number of substitutes from the remaining players
	 * @param array $avaliableSubs Array of single registrations that can be substitutes
	 * @param int $subCount Number of subs to be chosen
	 * @return array List of chosen substitutes' registration ids
	 */
	protected function randomlyChooseSubs($avaliableSubs, $subCount){
		$chosen = array();
		
		$keys = array_keys($avaliableSubs);
		while($subCount > 0){
			// randomly choose a registration
			$r = mt_rand(0, count($keys)-1);
			/* @var $registration Registration */
			$registration = $avaliableSubs[$keys[$r]];
			
			if(in_array($registration->getId(), $chosen)){
				// skip if it's already chosen
				continue;
			}
			$this->subs[] = $this->createPlayer($registration);
			$chosen[] = $registration->getId();
			$subCount--;
			unset($keys[$r]);
			$keys = array_values($keys);
		}
		return $chosen;
	}
	
	/**
	 * Creates players for all remaining registrations
	 * @param array $singles Array of single registrations recieved from RegistrationManager
	 * @param array $subRegistrations Array of registration id's of all substitutes
	 */
	protected function initSingles($singles, $subRegistrations){
		$this->singles = array();
		
		foreach($singles as $registration){
			/* @var $registration Registration */
			if(in_array($registration->getId(), $subRegistrations)){
				// skip if it is a substitute
				continue;
			}
			$this->singles[] = $this->createPlayer($registration);
		}
		
	}
	
	/**
	 * Creates a player for the passed registration
	 * @param Registration $registration
	 * @return Player
	 */
	protected function createPlayer(Registration $registration){
		$player = new Player();
		$player->setRegistration($registration);
		$player->setIsCaptain(false);
		return $player;
	}
	
	/**
	 * Initializes matched array with semi-complete teams
	 */
	protected function initMatched(){
		$this->matched = array_fill(0, 5, array());
		// set teams
		foreach($this->teams as $team){
			/* @var $team Team */
			$this->matched[count($team->getPlayers())][] = $team;
		}
		
		$icons = array_values($this->getRegistrationManager()->getAvailableIcons());
		// create teams from singles
		foreach($this->singles as $i => $player){
			/* @var $player Player */
			$team = new Team();
			$team->setName('Team '.(100 + $i));
			// choose icon
			$icon = mt_rand(0,count($icons)-1);
			$team->setIcon($icons[$icon]);
			unset($icons[$icon]);
			$icons = array_values($icons);
			$team->setPlayers(array($player));
			$this->matched[1][] = $team;
		}
		
		foreach(array_keys($this->matched) as $k){
			usort($this->matched[$k], array(Team::class, "compare"));
		}
	}
	
	/**
	 * Matches teams. Throws an exception if something went terribly wrong
	 * @throws \Exception
	 */
	public function match(){
		$this->init();
		$teamSize = $this->getTournamentProvider()->getTournament()->getRegistrationTeamSize();
		$teamCount = 0;
		foreach($this->matched as $teams){
			$teamCount += count($teams);
		}
		$c = 0;
		while(!$this->pickRound()){
			if($c > $teamSize * $teamCount){
				throw new \Exception('Too many picks!');
			}
			$c++;
		}
		
		$em = $this->getObjectManager();
		$c = 1;
		foreach($this->matched as $teams){
			foreach($teams as $team){ /* @var $team Team */
				foreach($team->getPlayers() as $player){
					$em->persist($player);
				}
				$team->setNumber($c)
						->setIsBlocked(false)
						->setAnmerkung('')
						->setTournament($this->getTournamentProvider()->getTournament());
				$em->persist($team);
				$c++;
			}
		}
		foreach($this->subs as $player){
			$em->persist($player);
		}
		$em->flush();
	}
	
	/**
	 * The weakest team can pick one other team to be combined with
	 * @return boolean
	 */
	protected function pickRound(){
		//$this->printCounts();
		$teamSize = $this->getTournamentProvider()->getTournament()->getRegistrationTeamSize();
		$minMatch = $this->getLowestPossibleMatch(); // pick largest teams first
		if($minMatch === false){
			throw new \Exception('Matching impossible!');
		}
		
		// get all teams that are allowed to pick
		$pickingTeams = array();
		for($i = 1; $i < $teamSize; $i++){
			$pickingTeams = array_merge($pickingTeams, $this->matched[$i]);
		}
		
		// get the weakest picking team
		/* @var $weakestTeam Team */
		$weakestTeam = null;
		foreach($pickingTeams as $team){
			/* @var $team Team */
			if(($weakestTeam === null || $weakestTeam->getAverageScore() > $team->getAverageScore()) && $this->canPick($team)){
				$weakestTeam = $team;
			}
		}
		if(!$weakestTeam){
			return true;
		}
		
		// get the largest teams that can be picked by weakest team
		$plCount = count($weakestTeam->getPlayers());
		$picks = $this->matched[$minMatch];
		if($teamSize - $plCount < $minMatch){
			$picks = $this->matched[$teamSize - $plCount];
			for($i = $teamSize < $plCount; $i >= 1; $i--){
				if(!empty($this->matched[$i])){
					$picks = $this->matched[$i];
					break;
				}
			}
		}
		// pick strongest available team
		if($this->pick($weakestTeam, $picks) === null){
			throw new \Exception('Pick round failed!');
		}
		return false;
	}
	
	/**
	 * Pick strongest team from $picks array
	 * @param Team $team Picking team
	 * @param array $picks Array of pickable teams
	 * @return Team|null
	 */
	protected function pick(Team $team, $picks){
		$randomPrefix = 'team 1';
		$teamSize = $this->getTournamentProvider()->getTournament()->getRegistrationTeamSize();
		usort($picks, array(Team::class, "compareAverage"));
		for($i = count($picks) - 1; $i >= 0; $i++){
			// do not combine with itself and only combine if team size is not exceeded
			if($team != $picks[$i] && count($team->getPlayers()) + count($picks[$i]->getPlayers()) <= $teamSize ){
				// ignore generated team names
				$teamname = $team->getName();
				if(substr(strtolower($teamname), 0, strlen($randomPrefix)) == $randomPrefix){
					$teamname = $picks[$i]->getName();
				}
				// combine teams
				return $this->combineTeams($team, $picks[$i], $teamname);
			}
		}
		return null;
	}
	
	protected function combineTeams(Team $a, Team $b, $teamname){
		$team = new Team();
		$team->setName($teamname);
		$team->setIcon($a->getIcon());
		$players = array();
		foreach($a->getPlayers() as $player){
			$players[] = $player;
			$player->setTeam($team);
		}
		foreach($b->getPlayers() as $player){
			$players[] = $player;
			$player->setTeam($team);
		}
		$team->setPlayers($players);
		
		$this->removeTeam($a);
		$this->removeTeam($b);
		$this->addTeam($team);
		
		return $team;
	}
	
	protected function removeTeam(Team $team){
		$plCount = count($team->getPlayers());
		unset($this->matched[$plCount][array_search($team, $this->matched[$plCount])]);
		$this->matched[$plCount] = array_values($this->matched[$plCount]);
	}
	
	protected function addTeam(Team $team){
		$this->matched[count($team->getPlayers())][] = $team;
	}
	
	protected function getLowestPossibleMatch(){
		$teamSize = $this->getTournamentProvider()->getTournament()->getRegistrationTeamSize();
		$minMatch = 1;
		while(!$this->matchPossible($minMatch)){
			if($minMatch > $teamSize / 2){
				// no match possible
				return false;
			}
			$minMatch++;
		}
		return $minMatch;
	}
	
	/**
	 * Returns path of team matches to be done.
	 * @return array
	 */
	protected function getMatchPath(){
		$teamSize = $this->getTournamentProvider()->getTournament()->getRegistrationTeamSize();
		
		$minMatch = 1;
		while(!$this->matchPossible($minMatch)){
			if($minMatch > $teamSize / 2){
				// no match possible
				return false;
			}
			$minMatch++;
		}
		
		$path = array();
		for($i = $teamSize - 1; $i >= $teamSize - $minMatch; $i--){
			$path[] = [$teamSize - $i, $i];
		}
		
		for($i = $minMatch; $i >= 1; $i++){ // size of team 1
			for($j = $teamSize - $i; $j >= 1; $j++){ // size of team 2
				$path[] = [$i, $j];
			}
		}
		return $path;
	}
	
	protected function matchPossible($minMatch = 1){
		$teamSize = $this->getTournamentProvider()->getTournament()->getRegistrationTeamSize();
		
		$teamCounts = array_fill(0, 5, 0);
		foreach($this->matched as $size => $teams){
			$teamCounts[$size] = count($teams);
		}
		$canPick = array();
		//$path = array();
		for($i = $minMatch; $i >= 1; $i--){ // size of team 1 
			for($j = $teamSize - $i; $j >= 1; $j--){ // size of team 2
				$missing = $teamSize - $j;
				$times = floor($missing / $i); // how often can size 1 be matched with size 2?
				// compute number of teams that are matched out of size 1 and size 2
				if($i != $j){
					if($teamCounts[$i] < $times){
						$times = $teamCounts[$i]; // if not at least $times size 1 teams are there, take all available
					}
					if($times == 0){
						$matchedTeams = 0;
					} else {
						$matchedTeams = min([floor($teamCounts[$i] / $times), $teamCounts[$j]]);
					}
				} else {
					$matchedTeams = floor($teamCounts[$i] / ($times + 1));
				}
				if($matchedTeams != 0){
					$canPick[$j] = true;
				}
				
				//$path[] = $i.'x'.$times.' + '.$j.' -- '.$matchedTeams; // Matching path for debugging
				
				// adjust team counts according to $times and $matchedTeams
				$teamCounts[$i * $times + $j] += $matchedTeams;
				$teamCounts[$i] -= $matchedTeams * $times;
				$teamCounts[$j] -= $matchedTeams;
			}
		}
		
		//var_dump($path);
		//var_dump($canPick);
		// check if there are no incomplete teams left
		for($i = 1; $i < $teamSize; $i++){
			if($teamCounts[$i] > 0){
				return false;
			}
		}
		$this->possible_picks = $canPick;
		return true;
	}
	
	/**
	 * Just a debug print
	 */
	public function printCounts(){
		$teamCounts = array();
		foreach($this->matched as $size => $teams){
			$teamCounts[$size] = count($teams);
		}
		var_dump($teamCounts);
	}
	
	protected function canPick(Team $team){
		return !empty($this->possible_picks[count($team->getPlayers())]);
	}
}
