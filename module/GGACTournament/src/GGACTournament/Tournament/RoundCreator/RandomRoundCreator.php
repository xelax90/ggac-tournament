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

/**
 * Randomly matches teams against each other if they have not played yet.
 *
 * @author schurix
 */
class RandomRoundCreator extends AbstractRoundCreator{
	
	/**
	 * @{inheritDoc}
	 */
	protected function _getDefaultConfig() {
		return array(
			'pointsPerGamePoint' => 0,
			'pointsPerMatchWin' => 1,
			'ignoreColors' => true
		);
	}

	/**
	 * @{inheritDoc}
	 */
	public function nextRound(Group $group, RoundConfig $roundConfig, AlreadyPlayedInterface $gameCheck) {
		// create round
		$round = $this->createRound($group, $roundConfig);
		
		// create array of non-blocked teams
		$teams = array();
		foreach($group->getTeams() as $team){
			/* @var $team \GGACTournament\Entity\Team */
			if(!$team->getIsBlocked()){
				$teams[] = $team;
			}
		}
		if(count($teams) % 2 != 0){
			$teams[] = null;
		}
		
		do {
			// shuffle teams array until we reach an acceptable state
			$roundOK = true;
			shuffle($teams);
			
			for($i = 0; $i+1 < count($teams); $i += 2){
				if($gameCheck->alreadyPlayed($teams[$i], $teams[$i+1])){
					$roundOK = false;
					break;
				}
			}
		} while (!$roundOK);
		
		// create matches for computed shuffle
		$matches = array();
		$number = 1;
		for($i = 0; $i+1 < count($teams); $i += 2){
			$matches[] = $this->createMatch($round, $roundConfig, $number, $teams[$i], $teams[$i+1]);
			$number++;
		}
		$round->setMatches($matches);
		
		// persist round, matches and games
		$em = $this->getObjectManager();
		$em->persist($round);
		$em->flush();
	}

}
