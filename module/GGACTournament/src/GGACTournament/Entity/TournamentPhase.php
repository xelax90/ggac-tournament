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

namespace GGACTournament\Entity;

use GGACTournament\Tournament\RoundCreator\AlreadyPlayedInterface;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * Each phase has it's own groups
 *
 * @ORM\Entity
 * @ORM\Table(name="tournamentphase")
 */
class TournamentPhase implements JsonSerializable, AlreadyPlayedInterface{
	const REGISTRATION_STATUS_OPEN = 'open';
	const REGISTRATION_STATUS_CLOSED = 'closed';
	const REGISTRATION_STATUS_NO_TEAMS = 'noTeams';
	const REGISTRATION_STATUS_SUB_ONLY = 'subOnly';
	const REGISTRATION_STATUS_SUB_ONLY_OR_TEAM = 'subOnlyTeam';
	
	const TOURNAMENT_STATUS_CLOSED = 'closed';
	const TOURNAMENT_STATUS_STARTED = 'started';
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="phases")
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
	 */
	protected $tournament;
	
	/**
	 * @ORM\Column(name="`number`", type="integer");
	 */
	protected $number;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $registrationState;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $tournamentState;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $defaultRoundtype;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $resetPoints;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $keepGroups;
	
	/**
	 * @ORM\OneToMany(targetEntity="Group", mappedBy="phase")
	 * @ORM\OrderBy({"number" = "ASC"})
	 */
	protected $groups;
	
	/**
	 * @ORM\Column(type="json_array")
	 */
	protected $tiebreaks;
	
	protected $tiebreakOrder = null;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @param int $id
	 * @return TournamentPhase
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}	
	
	/**
	 * @return Tournament
	 */
	public function getTournament() {
		return $this->tournament;
	}

	/**
	 * @return int
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getRegistrationState() {
		return $this->registrationState;
	}
	
	/**
	 * @return string
	 */
	public function getTournamentState() {
		return $this->tournamentState;
	}

	/**
	 * @return string
	 */
	public function getDefaultRoundtype() {
		return $this->defaultRoundtype;
	}

	/**
	 * @return array
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * @return boolean
	 */
	public function getResetPoints() {
		return $this->resetPoints;
	}
	
	/**
	 * @return boolean
	 */
	public function getKeepGroups() {
		return $this->keepGroups;
	}

	/**
	 * @return array
	 */
	public function getTiebreaks() {
		return $this->tiebreaks;
	}
	
	/**
	 * @param Tournament $tournament
	 * @return TournamentPhase
	 */
	public function setTournament($tournament) {
		$this->tournament = $tournament;
		return $this;
	}

	/**
	 * @param int $number
	 * @return TournamentPhase
	 */
	public function setNumber($number) {
		$this->number = $number;
		return $this;
	}

	/**
	 * @param string $name
	 * @return TournamentPhase
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @param string $registrationState
	 * @return TournamentPhase
	 */
	public function setRegistrationState($registrationState) {
		$this->registrationState = $registrationState;
		return $this;
	}
	
	/**
	 * @param string $tournamentState
	 * @return TournamentPhase
	 */
	public function setTournamentState($tournamentState) {
		$this->tournamentState = $tournamentState;
		return $this;
	}

	/**
	 * @param string $defaultRoundtype
	 * @return TournamentPhase
	 */
	public function setDefaultRoundtype($defaultRoundtype) {
		$this->defaultRoundtype = $defaultRoundtype;
		return $this;
	}
	
	/**
	 * @param array $groups
	 * @return TournamentPhase
	 */
	public function setGroups($groups) {
		$this->groups = $groups;
		return $this;
	}
	
	/**
	 * @param boolean $resetPoints
	 * @return TournamentPhase
	 */
	public function setResetPoints($resetPoints) {
		$this->resetPoints = $resetPoints;
		return $this;
	}
	
	/**
	 * @param boolean $keepGroups
	 * @return TournamentPhase
	 */
	public function setKeepGroups($keepGroups) {
		$this->keepGroups = $keepGroups;
		return $this;
	}

	/**
	 * @param array $tiebreaks
	 * @return TournamentPhase
	 */
	public function setTiebreaks($tiebreaks) {
		$this->tiebreaks = $tiebreaks;
		return $this;
	}
	
	/**
	 * Returns array of tiebreak keys in correct order for teamdata
	 * @param boolean $refresh Set true to force recomputation
	 * @return array
	 */
	public function getTiebreakOrder($refresh = false){
		if(null === $this->tiebreakOrder || $refresh){
			$order = array();
			foreach($this->getTiebreaks() as $tiebreak){
				if(class_exists($tiebreak)){
					$order[] = $tiebreak::getTiebreakKey();
				}
			}
			$this->tiebreakOrder = $order;
		}
		return $this->tiebreakOrder;
	}

	/**
	 * Returns json String
	 * @return string
	 */
	public function toJson(){
		$data = $this->jsonSerialize();
		return Json::encode($data, true, array('silenceCyclicalExceptions' => true));
	}
	
	/**
	 * Returns data to show in json
	 * @return array
	 */
	public function jsonSerialize() {
		return array(
			'id' => $this->getId(),
			'tournament_id' => $this->getTournament()->getId(),
			'number' => $this->getNumber(),
			'name' => $this->getName(),
			'registrationState' => $this->getRegistrationState(),
			'defaultRoundType' => $this->getDefaultRoundtype(),
			'resetPoints' => $this->getResetPoints(),
			'tiebreaks' => $this->getTiebreaks(),
		);
	}

	public function alreadyPlayed(Team $t1 = null, Team $t2 = null) {
		foreach($this->getGroups() as $group){
			/* @var $group Group */
			if($group->alreadyPlayed($t1, $t2)){
				return true;
			}
		}
		return false;
	}

}
