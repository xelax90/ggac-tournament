<?php
namespace GGACTournament\Tournament\RoundCreator;

use DateTime;
use Zend\Stdlib\AbstractOptions;

/**
 * Options passed to round creator
 *
 * @author schurix
 */
class RoundConfig extends AbstractOptions{
	protected $__strictMode__ = false;
	
	/** @var int */
	protected $gamesPerMatch = 3;
	
	/** @var int */
	protected $pointsPerGamePoint = 1;
	
	/** @var int */
	protected $pointsPerMatchWin = 0;
	
	/** @var int */
	protected $pointsPerMatchDraw = 0;
	
	/** @var int */
	protected $pointsPerMatchLoss = 0;
	
	/** @var int */
	protected $pointsPerMatchFree = 2;
	
	/** @var boolean */
	protected $ignoreColors = false;
	
	/** @var DateTime */
	protected $startDate;
	
	/** @var boolean */
	protected $isHidden = true;
	
	/** 
	 * Round duration in days
	 * @var int 
	 */
	protected $duration = 14;
	
	/** 
	 * Time for date arrangement in days
	 * @var int 
	 */
	protected $timeForDates = 7;
	
	public function getGamesPerMatch() {
		return $this->gamesPerMatch;
	}

	public function getPointsPerGamePoint() {
		return $this->pointsPerGamePoint;
	}

	public function getPointsPerMatchWin() {
		return $this->pointsPerMatchWin;
	}

	public function getPointsPerMatchDraw() {
		return $this->pointsPerMatchDraw;
	}

	public function getPointsPerMatchLoss() {
		return $this->pointsPerMatchLoss;
	}

	public function getPointsPerMatchFree() {
		return $this->pointsPerMatchFree;
	}

	public function getIgnoreColors() {
		return $this->ignoreColors;
	}
	
	/**
	 * @return DateTime
	 */
	public function getStartDate() {
		return $this->startDate;
	}

	public function getIsHidden() {
		return $this->isHidden;
	}

	public function getDuration() {
		return $this->duration;
	}

	public function getTimeForDates() {
		return $this->timeForDates;
	}

	public function setGamesPerMatch($gamesPerMatch) {
		$this->gamesPerMatch = $gamesPerMatch;
		return $this;
	}

	public function setPointsPerGamePoint($pointsPerGamePoint) {
		$this->pointsPerGamePoint = $pointsPerGamePoint;
		return $this;
	}

	public function setPointsPerMatchWin($pointsPerMatchWin) {
		$this->pointsPerMatchWin = $pointsPerMatchWin;
		return $this;
	}

	public function setPointsPerMatchDraw($pointsPerMatchDraw) {
		$this->pointsPerMatchDraw = $pointsPerMatchDraw;
		return $this;
	}

	public function setPointsPerMatchLoss($pointsPerMatchLoss) {
		$this->pointsPerMatchLoss = $pointsPerMatchLoss;
		return $this;
	}

	public function setPointsPerMatchFree($pointsPerMatchFree) {
		$this->pointsPerMatchFree = $pointsPerMatchFree;
		return $this;
	}

	public function setIgnoreColors($ignoreColors) {
		$this->ignoreColors = $ignoreColors;
		return $this;
	}

	public function setStartDate(DateTime $startDate) {
		$this->startDate = $startDate;
		return $this;
	}

	public function setIsHidden($isHidden) {
		$this->isHidden = $isHidden;
		return $this;
	}

	public function setDuration($duration) {
		$this->duration = $duration;
		return $this;
	}

	public function setTimeForDates($timeForDates) {
		$this->timeForDates = $timeForDates;
		return $this;
	}

}