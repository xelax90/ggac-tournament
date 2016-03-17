<?php
namespace GGACTournament\Entity;

use GGACTournament\Tournament\RoundCreator\AlreadyPlayedInterface;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Group
 *
 * @ORM\Entity
 * @ORM\Table(name="groups")
 */
class Group implements JsonSerializable, AlreadyPlayedInterface
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\ManyToOne(targetEntity="TournamentPhase", inversedBy="groups")
	 * @ORM\JoinColumn(name="tournamentPhase_id", referencedColumnName="id")
	 */
	protected $phase;
 	
	/**
	 * @ORM\Column(name="`number`", type="integer");
	 */
	protected $number;
 	
	/**
     * @ORM\OneToMany(targetEntity="GroupTeamMapping", mappedBy="group")
	 * @ORM\OrderBy({"seed" = "ASC"})
	 */
	protected $teamMappings;
 	
	/**
	 * @ORM\OneToMany(targetEntity="Round", mappedBy="group")
 	 * @ORM\OrderBy({"number" = "DESC"})
	 */
	protected $rounds;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return TournamentPhase
	 */
	public function getPhase() {
		return $this->phase;
	}
	
	/**
	 * @return int
	 */
	public function getNumber() {
		return $this->number;
	}
	
	/**
	 * @return array
	 */
	public function getTeamMappings() {
		return $this->teamMappings;
	}
	
	/**
	 * @return array
	 */
	public function getRounds() {
		return $this->rounds;
	}
	
	/**
	 * @param int $id
	 * @return Group
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @param TournamentPhase $phase
	 * @return Group
	 */
	public function setPhase($phase) {
		$this->phase = $phase;
		return $this;
	}

	/**
	 * @param int $number
	 * @return Group
	 */
	public function setNumber($number) {
		$this->number = $number;
		return $this;
	}

	/**
	 * @param array $teamMappings
	 * @return Group
	 */
	public function setTeamMappings($teamMappings) {
		$this->teamMappings = $teamMappings;
		return $this;
	}

	/**
	 * @param array $rounds
	 * @return Group
	 */
	public function setRounds($rounds) {
		$this->rounds = $rounds;
		return $this;
	}
	
	/**
	 * Returns array of teams ordered by seed
	 * @return array
	 */
	public function getTeams(){
		$teams = array();
		foreach($this->getTeamMappings() as $mapping){
			/* @var $mapping GroupTeamMapping */
			$teams[] = $mapping->getTeam();
		}
		return $teams;
	}
	
	/**
	 * Returns true if the teams already played in this group
	 * @param Team $t1
	 * @param Team $t2
	 * @return boolean
	 */
	public function alreadyPlayed(Team $t1 = null, Team $t2 = null){
		foreach($this->getRounds() as $round){
			if($round->alreadyPlayed($t1, $t2)){
				return true;
			}
		}
		return false;
	}
	
	
	public function getPreviousRound(Round $round = null){
		if($round === null){
			$round = $this->getLastRound();
		}
		/* @var $prevRound Round */
		$prevRound = null;
		$rounds = $this->getRounds();
		foreach($rounds as $rd){
			/* @var $rd Round */
			if($prevRound === null || $prevRound->getNumber() < $rd->getNumber()){
				if($rd->getNumber() < $round->getNumber()){
					$prevRound = $rd;
				}
			}
		}
		return $prevRound;
	}
	
	/**
	 * Computes the most recent round. Only takes visible rounds into account if $visibleOnly is not false.
	 * Returns null if no round exists
	 * @param boolean $visibleOnly Set to false if you also want to get hidden rounds
	 * @return Round|null
	 */
	public function getLastRound($visibleOnly = true){
		$rounds = $this->getRounds();
		/* @var $maxRound Round */
		$maxRound = null;
		foreach($rounds as $round){
			/* @var $round Round */
			if($maxRound === null || $maxRound->getNumber() < $round->getNumber()){
				if(!$round->getIsHidden() || !$visibleOnly){
					$maxRound = $round;
				}
			}
		}
		return $maxRound;
	}
	
	/**
	 * Returns last round with end date in the past
	 * @return RoundEntity
	 */
	public function getLastFinishedRound(){
		$rounds = $this->getRounds()->toArray();
		// sort rounds by round number in descending order
		usort($rounds, function($r1, $r2){return $r2->getNumber() - $r1->getNumber();});
		
		$now = new DateTime();
		foreach($rounds as $round){ // Iterate through rounds. Start with newest.
			/* @var $round Round */
			// compute end date
			$date = clone $round->getStartDate();
			$date->modify('+'.$round->getDuration().' days');
			
			// check if round ended
			if ($now <= $date) {
				return $round;
			}
		}
		return null;
	}
	
	/**
	 * Returns the visible round with highest number
	 * @return RoundEntity
	 */
	public function getCurrentRound(){
		$rounds = $this->getRounds()->toArray();
		if (empty($rounds)) {
			return null;
		}

		// sort rounds by round number in descending order
		usort($rounds, function($r1, $r2){return $r2->getNumber() - $r1->getNumber();});
		
		// find first visible round
		foreach($rounds as $round){
			/* @var $round Round */
			if (!$round->getIsHidden()) {
				return $round;
			}
		}
		
		return null;
	}
	
	/**
	 * Returns maximum round number
	 * @return int
	 */
	public function getMaxRoundNumber(){
		$round = $this->getLastRound(false);
		if($round){
			return $round->getNumber();
		}
		return 0;
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
	public function jsonSerialize(){
		$data = array(
			"id" => $this->getId(),
			"phase" => $this->getPhase(),
			"number" => $this->getNumber(),
			"teams" => $this->getTeams(),
			"rounds" => $this->getRounds(),
		);
		return $data;
	}
}