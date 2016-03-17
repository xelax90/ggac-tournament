<?php
namespace GGACTournament\Entity;

use GGACTournament\Tournament\Teamdata\Data as Teamdata;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Team
 *
 * @ORM\Entity(repositoryClass="GGACTournament\Model\TeamRepository")
 * @ORM\Table(name="team")
 */
class Team implements JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Tournament")
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
	 */
	protected $tournament;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $name;
 	
	/**
	 * @ORM\OneToMany(targetEntity="GroupTeamMapping", mappedBy="team")
	 */
	protected $groupMappings;
	
	/**
	 * @ORM\Column(name="`number`", type="integer");
	 */
	protected $number;
 	
	/**
	 * @ORM\Column(type="boolean");
	 */
	protected $isBlocked;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $icon;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $anmerkung;
 	
	/**
     * @ORM\ManyToOne(targetEntity="SkelletonApplication\Entity\User", inversedBy="teams")
	 * @ORM\JoinColumn(name="ansprechpartner_id", referencedColumnName="user_id")
	 */
	protected $ansprechpartner;
	
	/**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="team", cascade={"persist"})
 	 * @ORM\OrderBy({"isCaptain" = "DESC"})
	 */
	protected $players;
	
	/**
     * @ORM\OneToMany(targetEntity="Warning", mappedBy="team")
	 */
	protected $warnings;
	
	/** @var Teamdata */
	protected $data;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return Tournament
	 */
	public function getTournament() {
		return $this->tournament;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function getGroupMappings() {
		return $this->groupMappings;
	}

	/**
	 * @return int
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @return boolean
	 */
	public function getIsBlocked() {
		return $this->isBlocked;
	}

	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 * @return string
	 */
	public function getAnmerkung() {
		return $this->anmerkung;
	}

	/**
	 * @return \SkelletonApplication\Entity\User
	 */
	public function getAnsprechpartner() {
		return $this->ansprechpartner;
	}

	/**
	 * @return array
	 */
	public function getPlayers() {
		return $this->players;
	}

	/**
	 * @return array
	 */
	public function getWarnings() {
		return $this->warnings;
	}

	/**
	 * @return Teamdata
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * @param int $id
	 * @return Team
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * @param Tournament $tournament
	 * @return Team
	 */
	public function setTournament($tournament) {
		$this->tournament = $tournament;
		return $this;
	}

	/**
	 * @param string $name
	 * @return Team
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @param array $groupMappings
	 * @return Team
	 */
	public function setGroupMappings($groupMappings) {
		$this->groupMappings = $groupMappings;
		return $this;
	}

	/**
	 * @param int $number
	 * @return Team
	 */
	public function setNumber($number) {
		$this->number = $number;
		return $this;
	}

	/**
	 * @param boolean $isBlocked
	 * @return Team
	 */
	public function setIsBlocked($isBlocked) {
		$this->isBlocked = $isBlocked;
		return $this;
	}

	/**
	 * @param string $icon
	 * @return Team
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
		return $this;
	}

	/**
	 * @param string $anmerkung
	 * @return Team
	 */
	public function setAnmerkung($anmerkung) {
		$this->anmerkung = $anmerkung;
		return $this;
	}

	/**
	 * @param \SkelletonApplication\Entity\User $ansprechpartner
	 * @return Team
	 */
	public function setAnsprechpartner($ansprechpartner) {
		$this->ansprechpartner = $ansprechpartner;
		return $this;
	}

	/**
	 * @param array $players
	 * @return Team
	 */
	public function setPlayers($players) {
		$this->players = $players;
		return $this;
	}

	/**
	 * @param array $warnings
	 * @return Team
	 */
	public function setWarnings($warnings) {
		$this->warnings = $warnings;
		return $this;
	}

	/**
	 * @param Teamdata $data
	 * @return Team
	 */
	public function setData(Teamdata $data) {
		$this->data = $data;
		return $this;
	}

	/**
	 * Returns group for passed phase
	 * @param TournamentPhase $phase
	 * @return Group
	 */
	public function getGroup(TournamentPhase $phase){
		foreach($this->getGroupMappings() as $groupMapping){
			/* @var $groupMapping GroupTeamMapping */
			if($groupMapping->getGroup()->getPhase() === $phase){
				return $groupMapping->getGroup();
			}
		}
		return null;
	}
	
	/**
	 * Returns name of group for passed phase
	 * @param TournamentPhase $phase
	 * @return string
	 */
	public function getGroupName(TournamentPhase $phase){
		return 'Gruppe '.$this->getGroup($phase)->getNumber();
	}
	
	/**
	 * Returns sum of player scores for this team. Returns aveage score times 5 when more than 5 players in team
	 * @return int
	 */
	public function getScore(){
		$score = 0;
		if($this->getPlayers()){
			$count = 0;
			foreach($this->getPlayers() as $player){
				$count++;
				$score += $player->getScore();
			}
			if($count > 0){
				$score = floor($score / $count * 5);
			}
		}
		return $score;
	}
	
	/**
	 * Returns average player score
	 * @return float
	 */
	public function getAverageScore(){
		$score = 0;
		if($this->getPlayers()){
			$count = 0;
			foreach($this->getPlayers() as $player){
				$count++;
				$score += $player->getScore();
			}
			if($count > 0){
				$score = $score / $count;
			}
		}
		return $score;
	}
	
	/**
	 * Returns true if a captain exists in this team
	 * @return boolean
	 */
	public function hasCaptain(){
		foreach($this->getPlayers() as $player){
			if($player->getIsCaptain()){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Compares $a and $b using their score
	 * @param Team $a
	 * @param Team $b
	 * @return float
	 */
	public static function compare($a, $b){
		return $a->getScore() - $b->getScore();
	}
	
	/**
	 * Compares $a and $b using their average score
	 * @param Team $a
	 * @param Team $b
	 * @return float
	 */
	public static function compareAverage($a, $b){
		return ($a->getAverageScore() - $b->getAverageScore()) * 10;
	}
	
	/**
	 * Compares $a and $b using teamdata first, then compare
	 * @param Team $a
	 * @param Team $b
	 * @return float
	 */
	public static function comparePoints($a, $b){
		$dataA = $a->getData();
		$dataB = $b->getData();
		if(empty($dataA) || empty($dataB)){
			return self::compare($b, $a);
		}
		
		$dataCmp = Teamdata::compare($dataA, $dataB);
		if($dataCmp != 0){
			return $dataCmp;
		}
		
		return self::compare($b, $a);
	}

	/**
	 * Compares $a and $b using teamdata first, farberwartung, then compare
	 * @param Team $a
	 * @param Team $b
	 * @return float
	 */
	public static function compareFarberwartung($a, $b){
		$dataA = $a->getData();
		$dataB = $b->getData();
		if(empty($dataA) || empty($dataB)){
			return self::compare($b, $a);
		}
		
		$dataCmp = Teamdata::compare($dataA, $dataB);
		if($dataCmp != 0){
			return $dataCmp;
		}
		
		$erwartungen = array("+g" => 3, "+h" => 3, "g" => 2, "h" => 2, "-o" => 1, "+o" => 1, "o" => 0);
		if($dataB->getFarberwartung() != $dataA->getFarberwartung()){
			return $erwartungen[$dataB->getFarberwartung()] - $erwartungen[$dataA->getFarberwartung()];
		}
		
		return self::compare($b, $a);
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
			"name" => $this->getName(),
			"number" => $this->getNumber(),
			"isBlocked" => $this->getIsBlocked(),
			"icon" => $this->getIcon(),
			"anmerkung" => $this->getAnmerkung(),
			"ansprechpartner" => $this->getAnsprechpartner(),
		);
		return $data;
	}
}