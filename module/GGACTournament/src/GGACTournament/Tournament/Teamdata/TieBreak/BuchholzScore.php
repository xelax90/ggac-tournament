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

namespace GGACTournament\Tournament\Teamdata\TieBreak;

use GGACTournament\Entity\Group;
use GGACTournament\Entity\Round;

/**
 * Description of BuchholzScore
 *
 * @author schurix
 */
class BuchholzScore extends AbstractScore{
	const TIEBREAK_KEY = 'buchholz';
	
	/**
	 * {@inheritDoc}
	 */
	public static function getTiebreakKey(){
		return self::TIEBREAK_KEY;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function computeScore($refresh = false) {
		parent::computeScore();
		
		$teamdataManager = $this->getTeamdataManager();
		
		$teamdata = $teamdataManager->getTeamdata($refresh);
		if(!$refresh && $this->isComplete($teamdata)){
			// no need to compute all data again
			return;
		}
		
		$tournament = $this->getTournamentProvider()->getTournament();
		
		$opponents = array();
		foreach($tournament->getPhases() as $phase){
			/* @var $phase \GGACTournament\Entity\TournamentPhase */
			if($phase->getResetPoints()){
				// reset opponents only if scores are reset by phase
				$opponents = array();
			}
			foreach($phase->getGroups() as $group){
				/* @var $group Group */
				if(is_array($teamdata[$group->getId()][0])){
					// if round 0 is set, compute the Buchholz score for this round, too
					$this->computeBuchholz($teamdata, $group, 0, $opponents);
				}
				foreach($group->getRounds() as $round){
					/* @var $round Round */
					// compute all relevant opponents for the current round
					foreach($round->getMatches() as $match){
						/* @var $match \GGACTournament\Entity\Match */
						if(!$match->getTeamHome() || !$match->getTeamGuest()){
							// skip this match if it is a free game
							continue;
						}
						
						if(empty($opponents[$match->getTeamHome()->getId()])){
							$opponents[$match->getTeamHome()->getId()] = array();
						}
						if(empty($opponents[$match->getTeamGuest()->getId()])){
							$opponents[$match->getTeamGuest()->getId()] = array();
						}
						
						if(!in_array($match->getTeamGuest()->getId(), $opponents[$match->getTeamHome()->getId()])){
							$opponents[$match->getTeamHome()->getId()][] = $match->getTeamGuest()->getId();
						}
						if(!in_array($match->getTeamHome()->getId(), $opponents[$match->getTeamGuest()->getId()])){
							$opponents[$match->getTeamGuest()->getId()][] = $match->getTeamHome()->getId();
						}
					}
					
					$this->computeBuchholz($teamdata, $group, $round->getId(), $opponents);
				}
			}
		}
	}
	
	/**
	 * Compute the sum of all opponents' points which gives the Buchholz score
	 * @param array $teamdata Teamdata given by teamdata manager
	 * @param Group $group the group of teams that will be computed
	 * @param int $roundId the round id for the opponent scores
	 * @param type $opponents two-dimensional array that conatains all relevant opponents for all teams
	 */
	protected function computeBuchholz($teamdata, Group $group, $roundId, $opponents){
		foreach($group->getTeams() as $team){
			$buchholz = 0;
			/* @var $team \GGACTournament\Entity\Team */
			if(!empty($opponents[$team->getId()])){
				foreach($opponents[$team->getId()] as $opponentId){
					/* @var $currentData \GGACTournament\Tournament\Teamdata\Data */
					$opponentData = $teamdata[$group->getId()][$roundId][$opponentId];
					$buchholz += $opponentData->getPoints();
				}
			}
			$teamdata[$group->getId()][$roundId][$team->getId()]->setTiebreak(static::getTiebreakKey(), $buchholz);
		}
	}
	
	protected function isComplete($teamdata){
		foreach($teamdata as $roundData){
			foreach($roundData as $teamData){
				foreach($teamData as $data){
					/* @var $data \GGACTournament\Tournament\Teamdata\Data */
					if($data->getTiebreak(static::getTiebreakKey()) === null){
						return false;
					}
				}
			}
		}
		return true;
	}
	
}
