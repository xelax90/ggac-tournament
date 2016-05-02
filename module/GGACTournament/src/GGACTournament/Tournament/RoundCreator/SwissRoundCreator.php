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

use GGACTournament\Entity\Group;
use GGACTournament\Entity\Team;

/**
 * Description of SwissRoundCreator
 *
 * @author schurix
 */
class SwissRoundCreator extends AbstractRoundCreator{
	
	/**
	 * @{inheritDoc}
	 */
	protected function _getDefaultConfig() {
		return array(
		);
	}

	public function nextRound(Group $group, RoundConfig $roundConfig, AlreadyPlayedInterface $gameCheck) {
		$farberwartungen = array("+g" => -3, "g" => -2, "-o" => -1, "o" => 0, "+o" => 1, "h" => 2, "+h" => 3);
		
		$this->getApiDataManager()->setData();
		$this->getTeamdataManager()->injectTeamdata($group, null, true, true);
		$round = $this->createRound($group, $roundConfig);
		
		// create array of non-blocked teams
		$teams = array();
		foreach($group->getTeams() as $team){
			/* @var $team \GGACTournament\Entity\Team */
			if(!$team->getIsBlocked() && $team->hasCaptain()){
				$teams[] = $team;
			}
		}
		
		// group teams with equal points
		$punktegruppen = array();
		foreach($teams as $team){ /* @var $team Team */
			$punktegruppen[$team->getData()->getPoints()][] = $team;
		}
		krsort($punktegruppen);
		$punktegruppenKeys = array_keys($punktegruppen);
		$matches = array();
		$matchCount = 1;
		// iterate through point groups starting with the highest points
		foreach($punktegruppenKeys as $keyIndex => $gruppeIndex){
			$punktegruppe = $punktegruppen[$gruppeIndex];
			
			// sort current group by Farberwartung
			usort($punktegruppe, array(Team::class, "compareFarberwartung"));
			$matched = array();
			
			for($i = 0; $i < count($punktegruppe); $i++):
				/* @var $t1 Team */
				$t1 = $punktegruppe[$i];
			
				// skip team if it is already matched
				if(in_array($i, $matched)) continue;
				
				// get Farberwartung priority for the current team
				$besteVerteilung = $this->getBestColorMapping($t1->getData()->getFarberwartung());
				// find team with same points and best possible Farberwartung
				foreach($besteVerteilung as $erwartung){
					if(in_array($i, $matched)) break;
					
					for($j = 0; $j < count($punktegruppe); $j++){
						/* @var $t2 Team */
						$t2 = $punktegruppe[$j];
						
						if(
								$j == $i || // do not match team with itself
								in_array($j, $matched) || // do not match if $j is already matched
								($t2->getData()->getFarberwartung() != $erwartung && !$round->getIgnoreColors()) || // only match teams that have the currect Farberwartung or the round ignores colors
								$gameCheck->alreadyPlayed($t1, $t2) // do not match if teams already played
						){
							continue;
						}
						
						// determine team colors
						$teamHome = null;
						$teamGuest = null;
						if($farberwartungen[$t1->getData()->getFarberwartung()] < $farberwartungen[$t2->getData()->getFarberwartung()]){
							$teamHome = $t2;
							$teamGuest = $t1;
						} else {
							$teamHome = $t1;
							$teamGuest = $t2;
						}
						
						$matched[] = $i;
						$matched[] = $j;
						
						$match = $this->createMatch($round, $roundConfig, $matchCount, $teamHome, $teamGuest);
						$matches[] = $match;
						$matchCount++;
						break;
					}
				}
			endfor;
			
			// move all unmatched teams to next point group
			if(count($punktegruppe) - count($matched) > 0){
				
				// find next point group
				$nextGroup = null;
				if($keyIndex < count($punktegruppenKeys) - 1){
					$nextGroup = $punktegruppenKeys[$keyIndex + 1];
				}
				
				// Find all unmatched teams
				$unmatchedTeams = array();
				foreach($punktegruppe as $tnr => $team){
					if(!in_array($tnr, $matched)){
						$unmatchedTeams[] = array(array_search($team, $punktegruppen[$gruppeIndex]), $team);
					}
				}
				
				if($nextGroup !== null){
					// If next group exists, move teams
					foreach($unmatchedTeams as $unmatched){
						list($tnr, $team) = $unmatched;
						$punktegruppen[$nextGroup][] = $team;
						unset($punktegruppen[$gruppeIndex][$tnr]);
					}
				} else {
					// If last group reached, create free matches
					foreach($unmatchedTeams as $unmatched){							
						list($tnr, $team) = $unmatched;
						$match = $this->createMatch($round, $roundConfig, $matchCount, $team, null);
						$matches[] = $match;
					}
				}
			}
		}
		$round->setMatches($matches);
		$this->getObjectManager()->persist($round);
		$this->getObjectManager()->flush();
	}
	
	/**
	 * Returns an array of posiible Farberwartungen that can be matched with the passed Farberwartung ordered by priority. 
	 * @param string $farberwartung
	 * @return array
	 */
	protected function getBestColorMapping($farberwartung){
		switch($farberwartung):
			case "+h" : $besteVerteilung = array("+g", "g", "-o", "o", "+o", "h"); break;
			case "h"  : $besteVerteilung = array("g", "+g", "-o", "o", "+o", "h", "+h"); break;
			case "+o" : $besteVerteilung = array("+g", "g", "-o", "o", "+o", "h", "+h"); break;
			case "-o" : $besteVerteilung = array("+h", "h", "+o", "o", "-o", "g", "+g"); break;
			case "g"  : $besteVerteilung = array("+h", "h", "+o", "o", "-o", "g", "+g"); break;
			case "+g" : $besteVerteilung = array("+h", "h", "+o", "o", "-o", "g"); break;
			default   : $besteVerteilung = array("o", "-o", "+o", "g", "h", "+g", "+h"); break;
		endswitch;
		return $besteVerteilung;
	}
}
