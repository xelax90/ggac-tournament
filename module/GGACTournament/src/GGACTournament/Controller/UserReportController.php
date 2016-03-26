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
use GGACTournament\Entity\Player;
use GGACTournament\Form\MatchUserResultForm;
use GGACTournament\Form\MatchTimeForm;
use GGACTournament\Entity\Match;
use GGACTournament\Entity\Team;
use DoctrineModule\Stdlib\Hydrator\Strategy\DisallowRemoveByReference;

/**
 * Description of UserReportController
 *
 * @author schurix
 */
class UserReportController extends AbstractTournamentController{
	/** @var ApiDataManager */
	protected $apiDataManager;
	
	/** @var TeamdataManager */
	protected $teamdataManager;
	
	/** @var TournamentManager */
	protected $tournamentManager;
	
	protected $timeForms = array();
	protected $reportForms = array();
	
	protected $player;
	
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
	
	/**
	 * @return Team
	 */
	protected function getTeam(){
		$player = $this->getPlayer();
		if(!$player){
			return null;
		}
		return $player->getTeam();
	}
	
	/**
	 * @return Player
	 */
	protected function getPlayer(){
		if(null === $this->player){
			$tournament = $this->getTournamentProvider()->getTournament();
			if(!$tournament){
				return $this->player;
			}
			if(!$this->zfcUserAuthentication()->hasIdentity()){
				return $this->player;
			}
			// get user
			$user = $this->zfcUserAuthentication()->getIdentity();
			$em = $this->getObjectManager();
			/* @var $playerRepo \GGACTournament\Model\PlayerRepository */
			$playerRepo = $em->getRepository(Player::class);
			// get current tournament player
			$players = $playerRepo->getPlayerForUser($user, $tournament);
			if(!$players || count($players) == 0){
				return $this->player;
			}
			$player = $players[0];
			$this->player = $player;
		}
		return $this->player;
	}

	protected function _redirectToMatches(){
		return $this->redirect()->toRoute('matches');
	}

	protected function _redirectToMyMatches(){
		return $this->redirect()->toRoute('my-matches');
	}

	protected function _redirectToHome(){
		return $this->redirect()->toRoute('home');
	}
	
	public function generateReportForm(Match $match){
		if(!isset($this->reportForms[$match->getId()])){
			$formManager = $this->getFormManager();
			$team = $this->getTeam();

			if($match->getTeamHome() != $team && $match->getTeamGuest() != $team){
				return null;
			}
			
			if($match->getIsBlocked()){
				return null;
			}
			
			/* @var $form MatchUserResultForm */
			$form = $formManager->get(MatchUserResultForm::class, array(
				'isHome' => $match->getTeamHome() == $team,
			));
			/* @var $hydrator \DoctrineModule\Stdlib\Hydrator\DoctrineObject */
			$hydrator = $form->get('match')->getHydrator();
			$hydrator->addStrategy('games', new DisallowRemoveByReference());
			$form->setBindOnValidate(false);
			$form->bind($match);
			$form->setAttribute('action', $this->url()->fromRoute('my-matches', array('match_id' => $match->getId())));
			$form->setAttribute('method', 'post');
			$this->reportForms[$match->getId()] = $form;
		}
		return $this->reportForms[$match->getId()];
	}
	
	public function generateTimeForm(Match $match){
		if(!isset($this->timeForms[$match->getId()])){
			$formManager = $this->getFormManager();
			$team = $this->getTeam();

			if($match->getTeamHome() != $team && $match->getTeamGuest() != $team){
				return null;
			}
			
			if($match->getIsBlocked()){
				return null;
			}
			
			/* @var $form MatchUserResultForm */
			$form = $formManager->get(MatchTimeForm::class, array(
				'isHome' => $match->getTeamHome() == $team,
			));
			$form->setBindOnValidate(false);
			$form->bind($match);
			$form->setAttribute('action', $this->url()->fromRoute('my-matches', array('match_id' => $match->getId())));
			$form->setAttribute('method', 'post');
			$this->timeForms[$match->getId()] = $form;
		}
		return $this->timeForms[$match->getId()];
	}
	
	public function indexAction(){
		$tournament = $this->getTournamentProvider()->getTournament();
		if(!$tournament){
			return $this->_redirectToHome();
		}
		$team = $this->getTeam();
		if(!$team){
			return $this->_redirectToMatches();
		}
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		
		if($this->updateMatch()){
			return $this->_redirectToMyMatches();
		}
		
		return new ViewModel(array(
			'team' => $team,
			'teamdataManager' => $this->getTeamdataManager(),
			'tournament' => $tournament,
			'resultFormGenerator' => array($this, 'generateReportForm'),
			'timeFormGenerator' => array($this, 'generateTimeForm'),
			'match_id' => $matchId,
		));
	}
	
	protected function getMatch(){
        $matchId = $this->getEvent()->getRouteMatch()->getParam('match_id');
		$em = $this->getObjectManager();
		/* @var $match Match */
		$match = $em->getRepository(Match::class)->find((int)$matchId);
		if(!$match){
			return null;
		}
		return $match;
	}
	
	protected function updateMatch(){
		$request = $this->getRequest();
		if(!$request->isPost()){
			return false;
		}
		$match = $this->getMatch();
		if(!$match){
			return false;
		}
		$data = $request->getPost();
		if(!empty($data['match']['games'])){
			$form = $this->generateReportForm($match);
		} else {
			$form = $this->generateTimeForm($match);
		}
		
		if(!$form){
			return false;
		}
		
		$form->setData($data);
		if ($form->isValid()) {
			$player = $this->getPlayer();
			
			if($form instanceof MatchUserResultForm){ // Only captains can change reports
				$isHome = $form->getIsHome();
				foreach($match->getGames() as $i => $game){ /* @var $game \GGACTournament\Entity\Game */
					if($isHome && !empty($game->getMeldungHome()) && !$player->getIsCaptain() ){
						$form->get('match')->get('games')->get($i)->get('meldungHome')->setMessages(array('Nur ein Captain kann eine Meldung ändern'));
						return false;
					}
					if(!$isHome && !empty($game->getMeldungGuest()) && !$player->getIsCaptain() ){
						$form->get('match')->get('games')->get($i)->get('meldungGuest')->setMessages(array('Nur ein Captain kann eine Meldung ändern'));
						return false;
					}
					if(!empty($game->getReport()) ){
						$form->get('match')->get('games')->get($i)->get('meldungGuest')->setMessages(array('Das Spiel wurde bereits automatisch erfasst.'));
						return false;
					}
				}
			}
			
			$form->bindValues();
			
			$this->getObjectManager()->flush();
			
			if($form instanceof MatchTimeForm){
				/* @var $match Match */
				$isComplete = !empty($match->getTimeHome()) && !empty($match->getTimeGuest());
				$isConflict = $isComplete && $match->getTimeHome() != $match->getTimeGuest();
				if($isConflict){
					$this->flashMessenger()->addWarningMessage('WARNUNG: Zeit erfolgreich gesetzt, sie stimmt aber nicht mit der gegnerischen Zeit überein');
				} elseif($isComplete){
					$this->flashMessenger()->addSuccessMessage('Zeit erfolgreich eingetragen');
				} else {
					$this->flashMessenger()->addSuccessMessage('Zeit erfolgreich eingetragen, sie muss aber noch vom Gegnerteam bestätigt werden.');
				}
			} else {
				foreach($match->getGames() as $i => $game){ /* @var $game \GGACTournament\Entity\Game */
					if($game->getPointsBlue() !== null || $game->getPointsPurple() !== null){
						//continue;
					}
					$isHome = $form->getIsHome();
					if($isHome){
						$meldung = $game->getMeldungHome();
					} else {
						$meldung = $game->getMeldungGuest();
					}
					switch($meldung){
						case '1-0': case '+--': 
							if($game->getTeamBlue() == $match->getTeamHome()){
								$game->setPointsBlue(1)
										->setPointsPurple($meldung == '+--' ? null : 0);
							} elseif($game->getTeamPurple() == $match->getTeamHome()){
								$game->setPointsPurple(1)
										->setPointsBlue($meldung == '+--' ? null : 0);
							}
							break;
						case '0-1': case '--+': 
							if($game->getTeamBlue() == $match->getTeamHome()){
								$game->setPointsBlue($meldung == '--+' ? null : 0)
										->setPointsPurple(1);
							} elseif($game->getTeamPurple() == $match->getTeamHome()){
								$game->setPointsPurple($meldung == '--+' ? null : 0)
										->setPointsBlue(1);
							}
							break;
					}
				}
				$this->getObjectManager()->flush();
				$this->flashMessenger()->addSuccessMessage('Ergebnis erfolgreich gemeldet');
			}
			return true;
		} 
		$this->flashMessenger()->addErrorMessage('Fehler beim Eintragen.');
		return false;
	}
}
