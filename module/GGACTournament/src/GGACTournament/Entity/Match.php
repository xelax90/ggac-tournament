<?php
namespace GGACTournament\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A Player
 *
 * @ORM\Entity
 * @ORM\Table(name="matches")
 */
class Match implements JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
	 * @ORM\Column(name="`number`", type="integer");
	 */
	protected $number;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Round", inversedBy="matches")
	 * @ORM\JoinColumn(name="round_id", referencedColumnName="id")
	 */
	protected $round;
 	
	/**
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_home_id", referencedColumnName="id")
	 */
	protected $teamHome;
 	
	/**
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_guest_id", referencedColumnName="id")
	 */
	protected $teamGuest;
 	
	/**
	 * @ORM\Column(type="float", nullable=true);
	 */
	protected $pointsHome;
 	
	/**
	 * @ORM\Column(type="float", nullable=true);
	 */
	protected $pointsGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $anmerkung;
 	
	/**
	 * @ORM\Column(type="boolean");
	 */
	protected $isBlocked = false;
 	
	/**
	 * @ORM\Column(type="datetime", nullable=true);
	 */
	protected $timeHome;
 	
	/**
	 * @ORM\Column(type="datetime", nullable=true);
	 */
	protected $timeGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $foodleURL;
	
	/**
	 * @ORM\OneToMany(targetEntity="Game", mappedBy="match", cascade={"persist", "remove"})
	 */
	protected $games;
	
	public function __construct() {
		$this->games = new ArrayCollection();
	}
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getNumber() {
		return $this->number;
	}

	/**
	 * @return Round
	 */
	public function getRound() {
		return $this->round;
	}

	/**
	 * @return Team
	 */
	public function getTeamHome() {
		return $this->teamHome;
	}

	/**
	 * @return Team
	 */
	public function getTeamGuest() {
		return $this->teamGuest;
	}

	/**
	 * @return float
	 */
	public function getPointsHome() {
		return $this->pointsHome;
	}

	/**
	 * @return float
	 */
	public function getPointsGuest() {
		return $this->pointsGuest;
	}

	/**
	 * @return string
	 */
	public function getAnmerkung() {
		return $this->anmerkung;
	}

	/**
	 * @return boolean
	 */
	public function getIsBlocked() {
		return $this->isBlocked;
	}

	/**
	 * @return \DateTime
	 */
	public function getTimeHome() {
		return $this->timeHome;
	}

	/**
	 * @return \DateTime
	 */
	public function getTimeGuest() {
		return $this->timeGuest;
	}

	/**
	 * @return string
	 */
	public function getFoodleURL() {
		return $this->foodleURL;
	}

	/**
	 * @return array
	 */
	public function getGames() {
		return $this->games;
	}

	/**
	 * @param int $id
	 * @return Match
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @param int $number
	 * @return Match
	 */
	public function setNumber($number) {
		$this->number = $number;
		return $this;
	}

	/**
	 * @param Round $round
	 * @return Match
	 */
	public function setRound($round) {
		$this->round = $round;
		return $this;
	}

	/**
	 * @param Team $teamHome
	 * @return Match
	 */
	public function setTeamHome($teamHome) {
		$this->teamHome = $teamHome;
		return $this;
	}

	/**
	 * @param Team $teamGuest
	 * @return Match
	 */
	public function setTeamGuest($teamGuest) {
		$this->teamGuest = $teamGuest;
		return $this;
	}

	/**
	 * @param float $pointsHome
	 * @return Match
	 */
	public function setPointsHome($pointsHome) {
		$this->pointsHome = $pointsHome;
		return $this;
	}

	/**
	 * @param float $pointsGuest
	 * @return Match
	 */
	public function setPointsGuest($pointsGuest) {
		$this->pointsGuest = $pointsGuest;
		return $this;
	}

	/**
	 * @param string $anmerkung
	 * @return Match
	 */
	public function setAnmerkung($anmerkung) {
		$this->anmerkung = $anmerkung;
		return $this;
	}

	/**
	 * @param boolean $isBlocked
	 * @return Match
	 */
	public function setIsBlocked($isBlocked) {
		$this->isBlocked = $isBlocked;
		return $this;
	}

	/**
	 * @param \DateTime $timeHome
	 * @return Match
	 */
	public function setTimeHome($timeHome) {
		$this->timeHome = $timeHome;
		return $this;
	}

	/**
	 * @param \DateTime $timeGuest
	 * @return Match
	 */
	public function setTimeGuest($timeGuest) {
		$this->timeGuest = $timeGuest;
		return $this;
	}

	/**
	 * @param string $foodleURL
	 * @return Match
	 */
	public function setFoodleURL($foodleURL) {
		$this->foodleURL = $foodleURL;
		return $this;
	}

	/**
	 * @param array $games
	 * @return Match
	 */
	public function setGames($games) {
		$this->games = $games;
		return $this;
	}

	/**
	 * Returns timeHome only if both timeHome and timeGuest are set
	 * @return \DateTime|null
	 */
	public function getTime(){
		if(!empty($this->getTimeHome()) && !empty($this->getTimeGuest())){
			return $this->timeHome;
		}
		return null;
	}
	
	/**
	 * Sets timeHome
	 * @param \DateTime $time
	 * @return Match
	 */
	public function setTime($time){
		$this->timeHome = $time;
		return $this;
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
			"round_id" => $this->getRound()->getId(),
			"number" => $this->getNumber(),
			"teamHome" => $this->getTeamHome(),
			"teamGuest" => $this->getTeamGuest(),
			"pointsHome" => $this->getPointsHome(),
			"pointsGuest" => $this->getPointsGuest(),
			"anmerkung" => $this->getAnmerkung(),
			"isBlocked" => $this->getIsBlocked(),
			"time" => $this->getTime(),
			"foodleURL" => $this->getFoodleURL(),
		);
		return $data;
	}
}