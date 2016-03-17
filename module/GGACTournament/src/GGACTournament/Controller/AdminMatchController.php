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

use GGACTournament\Tournament\Teamdata\Manager as TeamdataManager;
use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use GGACTournament\Tournament\Manager as TournamentManager;

use GGACTournament\Entity\Match;
use GGACTournament\Form\MatchCommentForm;
use GGACTournament\Form\MatchResultForm;
use Zend\View\Model\ViewModel;

/**
 * Description of AdminRoundController
 *
 * @author schurix
 */
class AdminMatchController extends AbstractTournamentController{
	
	/** @var TournamentManager */
	protected $tournamentManager;
	
	/** @var ApiDataManager */
	protected $apiDataManager;
	
	/** @var TeamdataManager */
	protected $teamdataManager;
	
	protected function getTournamentManager() {
		return $this->tournamentManager;
	}

	protected function getApiDataManager() {
		return $this->apiDataManager;
	}
	
	protected function getTeamdataManager() {
		return $this->teamdataManager;
	}

	public function setTournamentManager(TournamentManager $tournamentManager) {
		$this->tournamentManager = $tournamentManager;
		return $this;
	}
	
	public function setApiDataManager(ApiDataManager $apiDataManager) {
		$this->apiDataManager = $apiDataManager;
		return $this;
	}
	
	public function setTeamdataManager(TeamdataManager $teamdataManager) {
		$this->teamdataManager = $teamdataManager;
		return $this;
	}

	public function matchAdminAction(){
		$tournament = $this->getTournamentProvider()->getTournament();
		if(!$tournament){
			return new ViewModel();
		}
		
		$this->getTournamentManager()->calculateScores(true);
		$this->getTeamdataManager()->injectTeamdata();
		$this->getApiDataManager()->setData();
		
		return new ViewModel(array('tournament' => $tournament));
	}
	
	protected function _redirectToMatches(){
		return $this->redirect()->toRoute('zfcadmin/matches');
	}
	
	public function matchBlockAction(){
		$tournament = $this->getTournamentProvider()->getTournament();
		if(!$tournament){
			return $this->_redirectToMatches();
		}
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		$em = $this->getObjectManager();
		/* @var $match Match */
		$match = $em->getRepository(Match::class)->find((int)$matchId);
		if(!$match){
			return $this->_redirectToMatches();
		}
		
		$match->setIsBlocked(true);
		$em->flush();
		$this->flashMessenger()->addSuccessMessage(sprintf($this->getTranslator()->translate('Match %d in round %d was successfully blocked'), $match->getNumber(), $match->getRound()->getNumber()));
		
		return $this->_redirectToMatches();
	}
	
	public function matchUnblockAction(){
		$tournament = $this->getTournamentProvider()->getTournament();
		if(!$tournament){
			return $this->_redirectToMatches();
		}
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		$em = $this->getObjectManager();
		/* @var $match Match */
		$match = $em->getRepository(Match::class)->find((int)$matchId);
		if(!$match){
			return $this->_redirectToMatches();
		}
		$match->setIsBlocked(false);
		$em->flush();
		$this->flashMessenger()->addSuccessMessage(sprintf($this->getTranslator()->translate('Match %d in round %d was successfully unblocked'), $match->getNumber(), $match->getRound()->getNumber()));
		
		return $this->_redirectToMatches();
	}
	
	public function matchCommentAction(){
		$tournament = $this->getTournamentProvider()->getTournament();
		if(!$tournament){
			return $this->_redirectToMatches();
		}
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		$em = $this->getObjectManager();
		/* @var $match Match */
		$match = $em->getRepository(Match::class)->find((int)$matchId);
		if(!$match){
			return $this->_redirectToMatches();
		}
		$form = $this->getFormManager()->get(MatchCommentForm::class);
		$form->setBindOnValidate(false);
		$form->bind($match);
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$form->bindValues();
				$em->flush();
				$this->flashMessenger()->addSuccessMessage(sprintf($this->getTranslator()->translate('Match %d in round %d was successfully commented'), $match->getNumber(), $match->getRound()->getNumber()));
				return $this->_redirectToMatches();
			}
        }
		return new ViewModel(array(
			'id' => $match->getId(), 
			'form' => $form
		));
	}
	
	public function matchSetResultAction(){
		$tournament = $this->getTournamentProvider()->getTournament();
		if(!$tournament){
			return $this->_redirectToMatches();
		}
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		$em = $this->getObjectManager();
		/* @var $match Match */
		$match = $em->getRepository(Match::class)->find((int)$matchId);
		if(!$match){
			return $this->_redirectToMatches();
		}
		$form = $this->getFormManager()->get(MatchResultForm::class);
		$form->setBindOnValidate(false);
		$form->bind($match);
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$form->bindValues();
				$match->setIsBlocked(true);
				$em->flush();
				$this->flashMessenger()->addSuccessMessage(sprintf($this->getTranslator()->translate('Result for match %d in round %d was successfully set'), $match->getNumber(), $match->getRound()->getNumber()));
				return $this->_redirectToMatches();
			}
        }
		return new ViewModel(array(
			'id' => $match->getId(), 
			'form' => $form
		));
	}
	
}
