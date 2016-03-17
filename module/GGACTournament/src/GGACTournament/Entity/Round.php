<?php
namespace GGACTournament\Entity;

use GGACTournament\Tournament\RoundCreator\AlreadyPlayedInterface;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Round
 *
 * @ORM\Entity
 * @ORM\Table(name="round")
 */
class Round implements JsonSerializable, AlreadyPlayedInterface
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
	 * @ORM\Column(type="boolean");
	 */
	protected $isHidden = true;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="groups")
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
	 */
	protected $group;
 	
	/**
     * @ORM\OneToMany(targetEntity="Match", mappedBy="round", cascade={"persist", "remove"})
	 */
	protected $matches;
 	
	/**
	 * @ORM\Column(name="roundType", type="string");
	 */
	protected $roundType;
	
	/**
	 * @ORM\Column(type="date");
	 */
	protected $startDate;
	
	/**
	 * Duration of the round in days
	 * @ORM\Column(type="integer");
	 */
	protected $duration = 14;
	
	/**
	 * The time, the teams have to add a date for matches in days.
	 * @ORM\Column(type="integer");
	 */
	protected $timeForDates = 7;
 	
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $gamesPerMatch = 3;
	
	/**
	 * @ORM\Column(type="float")
	 */
	protected $pointsPerGamePoint = 1;
	
	/**
	 * @ORM\Column(type="float")
	 */
	protected $pointsPerMatchWin = 0;
	
	/**
	 * @ORM\Column(type="float")
	 */
	protected $pointsPerMatchDraw = 0;
	
	/**
	 * @ORM\Column(type="float")
	 */
	protected $pointsPerMatchLoss = 0;
	
	/**
	 * @ORM\Column(type="float")
	 */
	protected $pointsPerMatchFree = 2;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $ignoreColors = false;
	
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
	 * @return boolean
	 */
	public function getIsHidden() {
		return $this->isHidden;
	}

	/**
	 * @return Group
	 */
	public function getGroup() {
		return $this->group;
	}

	/**
	 * @return array
	 */
	public function getMatches() {
		return $this->matches;
	}

	/**
	 * @return string
	 */
	public function getRoundType() {
		return $this->roundType;
	}

	/**
	 * @return \DateTime
	 */
	public function getStartDate() {
		return $this->startDate;
	}

	/**
	 * @return int
	 */
	public function getDuration() {
		return $this->duration;
	}

	/**
	 * @return int
	 */
	public function getTimeForDates() {
		return $this->timeForDates;
	}

	/**
	 * @return int
	 */
	public function getGamesPerMatch() {
		return $this->gamesPerMatch;
	}

	/**
	 * @return float
	 */
	public function getPointsPerGamePoint() {
		return $this->pointsPerGamePoint;
	}

	/**
	 * @return float
	 */
	public function getPointsPerMatchWin() {
		return $this->pointsPerMatchWin;
	}

	/**
	 * @return float
	 */
	public function getPointsPerMatchDraw() {
		return $this->pointsPerMatchDraw;
	}

	/**
	 * @return float
	 */
	public function getPointsPerMatchLoss() {
		return $this->pointsPerMatchLoss;
	}

	/**
	 * @return float
	 */
	public function getPointsPerMatchFree() {
		return $this->pointsPerMatchFree;
	}

	/**
	 * @return boolean
	 */
	public function getIgnoreColors() {
		return $this->ignoreColors;
	}
	
	/**
	 * @param int $id
	 * @return Round
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @param int $number
	 * @return Round
	 */
	public function setNumber($number) {
		$this->number = $number;
		return $this;
	}

	/**
	 * @param boolean $isHidden
	 * @return Round
	 */
	public function setIsHidden($isHidden) {
		$this->isHidden = $isHidden;
		return $this;
	}

	/**
	 * @param Group $group
	 * @return Round
	 */
	public function setGroup($group) {
		$this->group = $group;
		return $this;
	}

	/**
	 * @param array $matches
	 * @return Round
	 */
	public function setMatches($matches) {
		$this->matches = $matches;
		return $this;
	}

	/**
	 * @param string $rundType
	 * @return Round
	 */
	public function setType($rundType) {
		$this->roundType = $rundType;
		return $this;
	}

	/**
	 * @param \DateTime $startDate
	 * @return Round
	 */
	public function setStartDate($startDate) {
		$this->startDate = $startDate;
		return $this;
	}

	/**
	 * @param int $duration
	 * @return Round
	 */
	public function setDuration($duration) {
		$this->duration = $duration;
		return $this;
	}

	/**
	 * @param int $timeForDates
	 * @return Round
	 */
	public function setTimeForDates($timeForDates) {
		$this->timeForDates = $timeForDates;
		return $this;
	}

	/**
	 * @param int $gamesPerMatch
	 * @return Round
	 */
	public function setGamesPerMatch($gamesPerMatch) {
		$this->gamesPerMatch = $gamesPerMatch;
		return $this;
	}

	/**
	 * @param float $pointsPerGamePoint
	 * @return Round
	 */
	public function setPointsPerGamePoint($pointsPerGamePoint) {
		$this->pointsPerGamePoint = $pointsPerGamePoint;
		return $this;
	}

	/**
	 * @param float $pointsPerMatchWin
	 * @return Round
	 */
	public function setPointsPerMatchWin($pointsPerMatchWin) {
		$this->pointsPerMatchWin = $pointsPerMatchWin;
		return $this;
	}

	/**
	 * @param float $pointsPerMatchDraw
	 * @return Round
	 */
	public function setPointsPerMatchDraw($pointsPerMatchDraw) {
		$this->pointsPerMatchDraw = $pointsPerMatchDraw;
		return $this;
	}

	/**
	 * @param float $pointsPerMatchLoss
	 * @return Round
	 */
	public function setPointsPerMatchLoss($pointsPerMatchLoss) {
		$this->pointsPerMatchLoss = $pointsPerMatchLoss;
		return $this;
	}

	/**
	 * @param float $pointsPerMatchFree
	 * @return Round
	 */
	public function setPointsPerMatchFree($pointsPerMatchFree) {
		$this->pointsPerMatchFree = $pointsPerMatchFree;
		return $this;
	}

	/**
	 * @param boolean $ignoreColors
	 * @return Round
	 */
	public function setIgnoreColors($ignoreColors) {
		$this->ignoreColors = $ignoreColors;
		return $this;
	}

	
	/**
	 * Returns true if the teams already played against each other in this round
	 * @param Team $t1
	 * @param Team $t2
	 * @return boolean
	 */
	public function alreadyPlayed(Team $t1 = null, Team $t2 = null){
		foreach($this->getMatches() as $match){
			if($match->getTeamHome() == $t1 && $match->getTeamGuest() == $t2){
				return true;
			}
			if($match->getTeamHome() == $t2 && $match->getTeamGuest() == $t1){
				return true;
			}
		}
		return false;
	}
	
	public function getConfigString() {
		$config = array(
			"gamesPerMatch" => $this->getGamesPerMatch(),
			'pointsPerGamePoint' => $this->getPointsPerGamePoint(),
			'pointsPerMatchWin' => $this->getPointsPerMatchWin(),
			'pointsPerMatchDraw' => $this->getPointsPerMatchDraw(),
			'pointsPerMatchLoss' => $this->getPointsPerMatchLoss(),
			'pointsPerMatchFree' => $this->getPointsPerMatchFree(),
		);
		$res = '';
		foreach($config as $k => $v){
			if($v == 0){
				continue;
			}
			$res .= implode(' ', $this->camelCaseToWordArray($k)).' = '.$v.' <br>';
		}
		return $res;
	}
	
	private function camelCaseToWordArray($camelCase){
		$pieces = preg_split('/(?=[A-Z])/',$camelCase);
		foreach($pieces as $k => $piece){
			$pieces[$k] = lcfirst($piece);
		}
		$pieces[0] = ucfirst($pieces[0]);
		return $pieces;
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
			"number" => $this->getNumber(),
			"isHidden" => $this->getIsHidden(),
			"groupId" => $this->getGroup()->getId(),
			"roundType" => $this->getRoundType(),
			"startDate" => $this->getStartDate(),
			"duration" => $this->getDuration(),
			"timeForDates" => $this->getTimeForDates(),
			"gamesPerMatch" => $this->getGamesPerMatch(),
			'pointsPerGamePoint' => $this->getPointsPerGamePoint(),
			'pointsPerMatchWin' => $this->getPointsPerMatchWin(),
			'pointsPerMatchDraw' => $this->getPointsPerMatchDraw(),
			'pointsPerMatchLoss' => $this->getPointsPerMatchLoss(),
			'pointsPerMatchFree' => $this->getPointsPerMatchFree(),
			'ignoreColors' => $this->getIgnoreColors()
		);
		return $data;
	}
}