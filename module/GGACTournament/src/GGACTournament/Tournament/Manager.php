<?php

namespace GGACTournament\Tournament;

use GGACTournament\Tournament\Teamdata\Manager as TeamdataManager;
use GGACTournament\Tournament\Teamdata\TieBreak\Manager as TieBreakManager;
use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;

/**
 * Tournament manager
 *
 * @author schurix
 */
class Manager extends AbstractManager{
	
	/** @var TeamdataManager */
	protected $teamdataManager;
	
	/** @var ApiDataManager */
	protected $apiDataManager;
	
	/** @var TieBreakManager */
	protected $tieBreakManager;
	
	/**
	 * @return TeamdataManager
	 */
	public function getTeamdataManager() {
		return $this->teamdataManager;
	}

	/**
	 * @return ApiDataManager
	 */
	public function getApiDataManager() {
		return $this->apiDataManager;
	}
	
	/**
	 * @return TieBreakManager
	 */
	public function getTieBreakManager() {
		return $this->tieBreakManager;
	}
	
	/**
	 * @param TeamdataManager $teamdataManager
	 * @return Manager
	 */
	public function setTeamdataManager(TeamdataManager $teamdataManager) {
		$this->teamdataManager = $teamdataManager;
		return $this;
	}

	/**
	 * @param ApiDataManager $apiDataManager
	 * @return Manager
	 */
	public function setApiDataManager(ApiDataManager $apiDataManager) {
		$this->apiDataManager = $apiDataManager;
		return $this;
	}

	/**
	 * @param TieBreakManager $tieBreakManager
	 * @return Manager
	 */
	public function setTieBreakManager(TieBreakManager $tieBreakManager) {
		$this->tieBreakManager = $tieBreakManager;
		return $this;
	}

	public function calculateScores($refresh = false){
		// compute teamdata
		$this->getTeamdataManager()->getTeamdata($refresh);
		$computed = array();
		foreach($this->getTournamentProvider()->getTournament()->getPhases() as $phase){
			/* @var $phase \GGACTournament\Entity\TournamentPhase */
			$tiebreaks = $phase->getTiebreaks();
			foreach($tiebreaks as $tiebreak){
				if(in_array($tiebreak, $computed)){
					continue;
				}
				
				/* @var $score Teamdata\TieBreak\ScoreInterface */
				$score = $this->getTieBreakManager()->get($tiebreak);
				$score->computeScore($refresh);
				$computed[] = $tiebreak;
			}
		}
	}
}
