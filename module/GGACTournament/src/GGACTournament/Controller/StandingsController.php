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

namespace GGACTournament\Controller;

use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use GGACTournament\Tournament\Teamdata\Manager as TeamdataManager;
use GGACTournament\Tournament\Manager as TournamentManager;

use Zend\View\Model\ViewModel;

/**
 * Description of StandingsController
 *
 * @author schurix
 */
class StandingsController extends AbstractTournamentController{
	/** @var ApiDataManager */
	protected $apiDataManager;
	
	/** @var TeamdataManager */
	protected $teamdataManager;
	
	/** @var TournamentManager */
	protected $tournamentManager;
	
	public function getApiDataManager() {
		return $this->apiDataManager;
	}

	public function getTeamdataManager() {
		return $this->teamdataManager;
	}
	
	public function getTournamentManager() {
		return $this->tournamentManager;
	}

	public function setApiDataManager(ApiDataManager $apiDataManager) {
		$this->apiDataManager = $apiDataManager;
		return $this;
	}

	public function setTeamdataManager(TeamdataManager $teamdataManager) {
		$this->teamdataManager = $teamdataManager;
		return $this;
	}
	
	public function setTournamentManager(TournamentManager $tournamentManager) {
		$this->tournamentManager = $tournamentManager;
		return $this;
	}

	public function tableAction() {
		$this->getTournamentManager()->calculateScores();
		$this->getTeamdataManager()->injectTeamdata();
		$this->getApiDataManager()->setData();
		return new ViewModel(array(
			'loginForm' => $this->getLoginForm(),
			'tournament' => $this->getTournamentProvider()->getTournament(),
		));
	}
}
