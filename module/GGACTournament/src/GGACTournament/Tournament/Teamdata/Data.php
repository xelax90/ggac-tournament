<?php
namespace GGACTournament\Tournament\Teamdata;

use GGACTournament\Entity\Team;

class Data{
	/** @var Team */
	protected $team;
	
	/** @var int */
	protected $teamid;
	
	/** @var float */
	protected $points;
	
	/** @var int */
	protected $playedHome;
	
	/** @var int */
	protected $playedGuest;
	
	/** @var boolean */
	protected $previousGameHome;
	
	/** @var boolean */
	protected $penultimateGameHome;
	
	/** @var boolean */
	protected $hochgereiht;
	
	/** @var boolean */
	protected $runtergereiht;
	
	/** @var array */
	protected $tiebreaks;
	
	/** @var array */
	protected $tiebreakOrder;
	
	public function __construct(Data $data = null){
		if(!empty($data)){
			$this->setTeam($data->getTeam());
			$this->setPoints($data->getPoints());
			$this->setTiebreaks($data->getTiebreaks());
			$this->setPlayedHome($data->getPlayedHome());
			$this->setPlayedGuest($data->getPlayedGuest());
			$this->setPreviousGameHome($data->getPreviousGameHome());
			$this->setPenultimateGameHome($data->getPenultimateGameHome());
			$this->setTiebreakOrder($data->getTiebreakOrder());
		}
	}
	
	/**
	 * @return Team
	 */
	public function getTeam(){
		return $this->team;
	}
	
	/**
	 * @param Team $team
	 * @return Data
	 */
	public function setTeam(Team $team){
		$this->teamId = $team->getId();
		$this->team = $team;
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getTeamId(){
		return $this->teamId;
	}
	
	/**
	 * @return float
	 */
	public function getPoints(){
		return $this->points;
	}
	
	/**
	 * @param float $points
	 * @return Data
	 */
	public function setPoints($points){
		$this->points = $points;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPlayedHome(){
		return $this->playedHome;
	}

	/**
	 * @param int $playedHome
	 * @return Data
	 */
	public function setPlayedHome($playedHome){
		$this->playedHome = $playedHome;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getPlayedGuest(){
		return $this->playedGuest;
	}

	/**
	 * @param int $playedGuest
	 * @return Data
	 */
	public function setPlayedGuest($playedGuest){
		$this->playedGuest = $playedGuest;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getPreviousGameHome(){
		return $this->previousGameHome;
	}

	/**
	 * @param boolean $previousGameHome
	 * @return Data
	 */
	public function setPreviousGameHome($previousGameHome){
		$this->previousGameHome = $previousGameHome;
		return $this;
	}
	
	/**
	 * @return boolean
	 */
	public function getPenultimateGameHome(){
		return $this->penultimateGameHome;
	}
	
	/**
	 * @param boolean $penultimateGameHome
	 * @return Data
	 */
	public function setPenultimateGameHome($penultimateGameHome){
		$this->penultimateGameHome = $penultimateGameHome;
		return $this;
	}
	
	/**
	 * Helper for round creator
	 * @return boolean
	 */
	public function getHochgereiht(){
		return $this->hochgereiht;
	}
	
	/**
	 * Helper for round creator
	 * @param boolean $hochgereiht
	 * @return Data
	 */
	public function setHochgereiht($hochgereiht){
		$this->hochgereiht = $hochgereiht;
		return $this;
	}
	
	/**
	 * Helper for round creator
	 * @return boolean
	 */
	public function getRuntergereiht(){
		return $this->runtergereiht;
	}
	
	/**
	 * Helper for round creator
	 * @param boolean $runtergereiht
	 * @return Data
	 */
	public function setRuntergereiht($runtergereiht){
		$this->runtergereiht = $runtergereiht;
		return $this;
	}
	
	/**
	 * Returns tiebreak scores
	 * @return array
	 */
	public function getTiebreaks() {
		return $this->tiebreaks;
	}
	
	/**
	 * Sets all tiebreak scores
	 * @param array $tiebreaks
	 * @return Data
	 */
	public function setTiebreaks($tiebreaks) {
		$this->tiebreaks = $tiebreaks;
		return $this;
	}
	
	/**
	 * Returns array of tiebreak keys for ordering
	 * @return array
	 */
	public function getTiebreakOrder() {
		return $this->tiebreakOrder;
	}
	
	/**
	 * Sets tiebreak order array
	 * @param array $tiebreakOrder
	 * @return Data
	 */
	public function setTiebreakOrder($tiebreakOrder) {
		$this->tiebreakOrder = $tiebreakOrder;
		return $this;
	}

	/**
	 * Sets the tiebreak score for the passed key
	 * @param string $key
	 * @param float $score
	 * @return Data
	 */
	public function setTiebreak($key, $score){
		$this->tiebreaks[$key] = $score;
		return $this;
	}
	
	/**
	 * Returns the tiebreak score for the passed key if present. Otherwise returns null
	 * @param string $key
	 * @return float|null
	 */
	public function getTiebreak($key){
		if(isset($this->tiebreaks[$key])){
			return $this->tiebreaks[$key];
		}
		return null;
	}

	/**
	 * Helper for round creator
	 * @return boolean
	 */
	public function isFloater(){
		return $this->getRuntergereiht() || $this->getHochgereiht();
	}
	
	/**
	 * Returns difference between playedHome and playedGuest
	 * @return int
	 */
	public function getFarbverteilung(){
		return $this->getPlayedHome() - $this->getPlayedGuest();
	}
	
	/**
	 * Returns color expectation of the team following these rules:
	 * +h (+g): Team must have home (guest) in the next match
	 * h (g): Team played more home (guest) in previous matches and should have guest (home) if possible
	 * +o (-o): Team played equal number of home and guest games, but had guest (home) in the previous match.
	 * o: No games played yet
	 * @return string
	 */
	public function getFarberwartung(){
		if($this->getFarbverteilung() < -1)
			return "+h";
		if($this->getFarbverteilung() > 1)
			return "+g";
		
		if($this->previousGameHome === false && $this->penultimateGameHome === false)
			return "+h";
		if($this->previousGameHome === true && $this->penultimateGameHome === true)
			return "+g";
		
		if($this->getFarbverteilung() < 0)
			return "h";
		if($this->getFarbverteilung() > 0)
			return "g";
		
		if($this->previousGameHome === false)
			return "+o";
		if($this->previousGameHome === true)
			return "-o";
		
		return "o";
	}
	
	public static function compare(Data $a, Data $b){
		if(!$a && !$b){
			return 0;
		}
		
		if(!$a && $b){
			return 1;
		}
		
		if(!$b && $a){
			return -1;
		}
		
		if($a->getPoints() != $b->getPoints()){
			return $b->getPoints() - $a->getPoints();
		}
		
		if($a->getTiebreakOrder() != $b->getTiebreakOrder()){
			return 0;
		}
		
		foreach($a->getTiebreakOrder() as $tiebreak){
			$tA = $a->getTiebreak($tiebreak);
			$tB = $b->getTiebreak($tiebreak);
			if($tA === $tB){
				continue;
			}
			
			if($tA !== null && $tB === null){
				return -1;
			}
			
			if($tA === null && $tB !== null){
				return 1;
			}
			
			return $tB - $tA;
		}
		
		return 0;
	}
	
	/**
	 * Magic function for serialization. Do not save team, hochgereiht and runtergereiht
	 * @return array
	 */
	public function __sleep(){
		return array('teamid', 'points', 'tiebreakOrder', 'playedHome', 'playedGuest', 'previousGameHome', 'penultimateGameHome');
	}
}