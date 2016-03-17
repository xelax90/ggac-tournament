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

use XelaxAdmin\Controller\ListController;
use GGACTournament\Tournament\Phase\Manager as PhaseManager;
use GGACTournament\Tournament\Teamdata\TieBreak\BuchholzScore;

/**
 * Description of AdminTournamentPhaseController
 *
 * @author schurix
 */
class AdminTournamentPhaseController extends ListController{
	
	/** @var PhaseManager */
	protected $phaseManager;
	
	public function getPhaseManager() {
		return $this->phaseManager;
	}

	public function setPhaseManager(PhaseManager $phaseManager) {
		$this->phaseManager = $phaseManager;
		return $this;
	}
	
	protected function _preUpdate($item) {
		parent::_preUpdate($item);
		$data = $this->getRequest()->getPost()->toArray();
		if(empty($data['tournamentphase']['tiebreaks'])){
			$item->setTiebreaks(array());
		}
	}
	
	protected function _preCreate($item) {
		/* @var $item \GGACTournament\Entity\TournamentPhase */
		parent::_preCreate($item);
	}
	
	protected function _postCreate($item) {
		/* @var $item \GGACTournament\Entity\TournamentPhase */
		parent::_postCreate($item);
		
		$tournament = $item->getTournament();
		$this->getPhaseManager()->initPhase($item, $tournament->getPreviousPhase($item));
	}
	
}
