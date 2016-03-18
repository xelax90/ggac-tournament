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

namespace GGACTournament\Tournament\Phase;

use GGACTournament\Entity\TournamentPhase;
use GGACTournament\Entity\Group;
use GGACTournament\Entity\GroupTeamMapping;
use GGACTournament\Tournament\AbstractManager;
use GGACTournament\Entity\Tournament;

/**
 * Description of Manager
 *
 * @author schurix
 */
class Manager extends AbstractManager{
	
	/**
	 * Starts a new phase
	 * 
	 * @param string $name
	 * @param string $registrationState
	 * @param string $defaultRoundtype
	 * @param boolean $ignoreBlocked If true, the blocked teams will not be copied when copying groups
	 * @return TournamentPhase The created phase
	 */
	public function nextPhase($name, $registrationState, $defaultRoundtype, $ignoreBlocked = true){
		$em = $this->getEntityManager();
		
		$tournament = $this->getTournamentProvider()->getTournament();
		$currentPhase = $tournament->getCurrentPhase();
		
		$phase = new TournamentPhase();
		$phase->setName($name)
				->setNumber($currentPhase->getNumber() + 1)
				->setRegistrationState($registrationState)
				->setDefaultRoundtype($defaultRoundtype)
				->setTournament($tournament);
		$em->persist($phase);
		$em->flush($phase);
		
		return $this->initPhase($phase, $currentPhase, $ignoreBlocked);
	}
	
	public function initPhase(TournamentPhase $phase, TournamentPhase $currentPhase = null, $ignoreBlocked = true){
		if($phase->getKeepGroups() && $currentPhase){
			$groups = $currentPhase->getGroups();
			$newGroups = array();
			// copy all groups
			foreach($groups as $group){
				$newGroup = $this->copyGroup($phase, $group, $ignoreBlocked);
				$newGroups[] = $newGroup;
			}
			$phase->setGroups($newGroups);
		}
		$this->getObjectManager()->flush();
		return $phase;
	}
	
	/**
	 * Creates new group for passed phase and puts all teams from the passed group into the new one.
	 * @param TournamentPhase $phase
	 * @param Group $group
	 * @param boolean $ignoreBlocked
	 * @return Group
	 */
	protected function copyGroup(TournamentPhase $phase, Group $group, $ignoreBlocked = true){
		$em = $this->getEntityManager();
		
		/* @var $group Group */
		$newGroup = new Group();
		$newGroup->setNumber($group->getNumber())
				->setPhase($phase);
		$em->persist($newGroup);
		$em->flush($newGroup);

		$mappings = array();
		foreach($group->getTeamMappings() as $mapping){
			/* @var $mapping GroupTeamMapping */

			if($ignoreBlocked && $mapping->getTeam()->getIsBlocked()){
				continue;
			}
			$newMapping = new GroupTeamMapping();
			$newMapping->setGroup($newGroup)
					->setTeam($mapping->getTeam())
					->setSeed($mapping->getSeed());
			$mappings[] = $newMapping;
			$em->persist($newMapping);
		}
		$newGroup->setTeamMappings($mappings);
		
		return $newGroup;
	}
	
}
