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

use Zend\View\Model\ViewModel;
use GGACTournament\Tournament\Manager as TournamentManager;
use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use GGACTournament\Tournament\Teamdata\Manager as TeamdataManager;
use GGACTournament\Tournament\TeamMatcher\TeamMatcher;

use GGACTournament\Entity\Team;
use GGACTournament\Entity\Warning;
use GGACTournament\Entity\Player;

use GGACTournament\Form\TeamForm;
use GGACTournament\Form\WarningForm;
use GGACTournament\Form\TeamCommentForm;
use GGACTournament\Form\SubstituteSelectForm;

/**
 * Description of AdminTeamController
 *
 * @author schurix
 */
class AdminTeamController extends AbstractTournamentController{
	
	/** @var TournamentManager */
	protected $tournamentManager;
	
	/** @var ApiDataManager */
	protected $apiDataManager;
	
	/** @var TeamdataManager */
	protected $teamdataManager;
	
	/** @var TeamMatcher */
	protected $teamMatcher;
	
	protected function _redirectToTeams(){
        $start = $this->getEvent()->getRouteMatch()->getParam('routeStart');
		return $this->redirect()->toRoute('zfcadmin/teams', array('routeStart' => $start));
	}

	public function indexAction() {
        $start = $this->getEvent()->getRouteMatch()->getParam('routeStart');
		$tournament = $this->getTournamentProvider()->getTournament();
		
		$this->getApiDataManager()->setData();
		$this->getTournamentManager()->calculateScores();
		$this->getTeamdataManager()->injectTeamdata();
		
		return new ViewModel(array(
			'onlyMine' => ($start === 'my-teams'),
			'tournament' => $tournament,
		));
	}
	
	public function teamEditAction(){
		$team = $this->getTeam();
		if(!$team){
			return $this->_redirectToTeams();
		}
        $start = $this->getEvent()->getRouteMatch()->getParam('routeStart');
		
		$form = $this->getFormManager()->get(TeamForm::class);
		if($this->editTeam($form, $team)){
			$this->addSuccess(gettext_noop('Team %s was successfully edited'), [$team->getName()] );
			return $this->_redirectToTeams();
		}
		return new ViewModel(array(
			'onlyMine' => ($start === 'my-teams'),
			'team' => $team,
			'form' => $form,
		));
	}
	
	public function teamCreateAction(){
        $start = $this->getEvent()->getRouteMatch()->getParam('routeStart');
		$em = $this->getObjectManager();
		$form = $this->getFormManager()->get(TeamForm::class);
        $request = $this->getRequest();
		
		if($request->isPost()){
			$team = new Team();
			$data = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
	        $form->bind($team);
	        $form->setData($data);
			if($form->isValid()){
				$em->persist($team);
				$em->flush();
				$this->addSuccess(gettext_noop('Team %s was successfully created'), [$team->getName()] );
				return $this->_redirectToTeams();
			}
		}
		
		return new ViewModel(array(
			'onlyMine' => ($start === 'my-teams'),
			'form' => $form,
		));
	}
	
	public function teamWarnAction(){
		$team = $this->getTeam();
		if(!$team){
			return $this->_redirectToTeams();
		}
        $start = $this->getEvent()->getRouteMatch()->getParam('routeStart');
		
		$em = $this->getObjectManager();
		$form = $this->getFormManager()->get(WarningForm::class);
		$request = $this->getRequest();
		if($request->isPost()){
			$warning = new Warning();
			$form->bind($warning);
			$form->setData($request->getPost());
			if($form->isValid()){
				$warning->setTeam($team);
				
				$em->persist($warning);
				$em->flush();
				$this->addSuccess(gettext_noop('Team %s was successfully warned'), [$team->getName()] );
				return $this->_redirectToTeams();
			}
		}
		
		return new ViewModel(array(
			'onlyMine' => ($start === 'my-teams'),
			'team' => $team,
			'form' => $form
		));
	}
	
	public function playerWarnAction(){
		$player = $this->getPlayer();
		if(!$player){
			return $this->_redirectToTeams();
		}
        $start = $this->getEvent()->getRouteMatch()->getParam('routeStart');
		
		$em = $this->getObjectManager();
		$form = $this->getFormManager()->get(WarningForm::class);
		$request = $this->getRequest();
		if($request->isPost()){
			$warning = new Warning();
			$form->bind($warning);
			$form->setData($request->getPost());
			if($form->isValid()){
				$warning->setPlayer($player);
				
				$em->persist($warning);
				$em->flush();
				$this->addSuccess(gettext_noop('Player %s was successfully warned'), [$player->getRegistration()->getSummonerName()] );
				return $this->_redirectToTeams();
			}
		}
		
		return new ViewModel(array(
			'onlyMine' => ($start === 'my-teams'),
			'player' => $player,
			'form' => $form
		));
	}
	
	public function playerMakeCaptainAction(){
		$player = $this->getPlayer();
		if(!$player){
			return $this->_redirectToTeams();
		}
		if(!$player->getTeam()){
			return $this->_redirectToTeams();
		}
		
		foreach($player->getTeam()->getPlayers() as $pl){
			/* @var $pl Player */
			$pl->setIsCaptain(false);
		}
		$player->setIsCaptain(true);
		$this->getObjectManager()->flush();
		$this->addSuccess(gettext_noop('Player %s is now captain'), [$player->getRegistration()->getSummonerName()] );
		return $this->_redirectToTeams();
	}
	
	public function warningDeleteAction(){
        $warning_id = $this->getEvent()->getRouteMatch()->getParam('warning_id');
		$em = $this->getObjectManager();
		/* @var $warning Warning */
		$warning = $em->getRepository(Warning::class)->find((int) $warning_id);
		if(!$warning){
			return $this->_redirectToTeams();
		}
		$warningFor = '';
		if($warning->getTeam()){
			$warningFor = 'team '. $warning->getTeam()->getName();
		} elseif($warning->getPlayer()) {
			$warningFor = 'player '.$warning->getPlayer()->getRegistration()->getSummonerName();
		}
		$em->remove($warning);
		$em->flush();
		$this->addSuccess(gettext_noop('Warning for %s was successfully deleted'), [$warningFor] );
		return $this->_redirectToTeams();
	}
	
	public function teamCommentAction(){
        $start = $this->getEvent()->getRouteMatch()->getParam('routeStart');
		$team = $this->getTeam();
		if(!$team){
			return $this->_redirectToTeams();
		}
		
		$form = $this->getFormManager()->get(TeamCommentForm::class);
		if($this->editTeam($form, $team)){
			$this->addSuccess(gettext_noop('Team %s was successfully edited'), [$team->getName()] );
			return $this->_redirectToTeams();
		}
		return new ViewModel(array(
			'onlyMine' => ($start === 'my-teams'),
			'team' => $team,
			'form' => $form,
		));
	}
	
	public function teamBlockAction(){
		$team = $this->getTeam();
		if(!$team){
			return $this->_redirectToTeams();
		}
		$team->setIsBlocked(true);
		$this->getObjectManager()->flush();
		$this->addSuccess(gettext_noop('Team %s was successfully blocked'), [$team->getName()] );
		return $this->_redirectToTeams();
	}
	
	public function teamUnblockAction(){
		$team = $this->getTeam();
		if(!$team){
			return $this->_redirectToTeams();
		}
		$team->setIsBlocked(false);
		$this->getObjectManager()->flush();
		$this->addSuccess(gettext_noop('Team %s was successfully unblocked'), [$team->getName()] );
		return $this->_redirectToTeams();
	}
	
	public function playerMakeSubAction(){
		$player = $this->getPlayer();
		if(!$player){
			return $this->_redirectToTeams();
		}
		$player->setTeam(null);
		$this->getObjectManager()->flush();
		$this->addSuccess(gettext_noop('Player %s is now substitute'), [$player->getRegistration()->getSummonerName()] );
		return $this->_redirectToTeams();
	}
	
	public function teamAddSubAction(){
		$team = $this->getTeam();
		if(!$team){
			return $this->_redirectToTeams();
		}
        $start = $this->getEvent()->getRouteMatch()->getParam('routeStart');
		$form = $this->getFormManager()->get(SubstituteSelectForm::class);
        $request = $this->getRequest();
		if($request->isPost()){
			$data = $request->getPost();
			$form->setData($data);
			if($form->isValid()){
				$data = $form->getData();
				$player = $this->getPlayer($data['substitute']);
				$player->setTeam($team);
				$this->getObjectManager()->flush();
				$this->addSuccess(gettext_noop('Player %s successfully added to team %s'), [$player->getRegistration()->getSummonerName(), $team->getName()]);
				return $this->_redirectToTeams();
			}
		}
		return new ViewModel(array(
			'onlyMine' => ($start === 'my-teams'),
			'team' => $team,
			'form' => $form,
		));
	}
	
	public function teamMatcherAction(){
		$confirm = $this->getEvent()->getRouteMatch()->getParam('confirm');
		$possible = true;
		$tournament = $this->getTournamentProvider()->getTournament();
		if($tournament->getCurrentPhase()->getTournamentState() == \GGACTournament\Entity\TournamentPhase::TOURNAMENT_STATUS_STARTED){
			$possible = false;
		}
		$teams = $tournament->getTeams();
		if(!$confirm || !$possible){
			return new ViewModel(array('possible' => $possible));
		}
		
		$em = $this->getObjectManager();
		foreach($teams as $team){ /* @var $team Team */
			foreach($team->getPlayers() as $player){
				$em->remove($player);
			}
			$em->remove($team);
		}
		$em->flush();
		foreach($tournament->getRegistrations() as $reg){
			$em->refresh($reg);
		}
		$this->getTeamMatcher()->match();
		return new ViewModel(array('ready' => true));
	}
	
	/**
	 * @param int $team_id
	 * @return Team
	 */
	protected function getTeam($team_id = null){
		if(!$team_id){
	        $team_id = $this->getEvent()->getRouteMatch()->getParam('team_id');
		}
		$em = $this->getObjectManager();
		$team = $em->getRepository(Team::class)->find((int) $team_id);
		return $team;
	}
	
	/**
	 * @param int $player_id
	 * @return Player
	 */
	protected function getPlayer($player_id = null){
		if(!$player_id){
	        $player_id = $this->getEvent()->getRouteMatch()->getParam('player_id');
		}
		$em = $this->getObjectManager();
		$player = $em->getRepository(Player::class)->find((int) $player_id);
		return $player;
	}
	
	
	protected function editTeam($form, Team $team){
		/* @var $form \Zend\Form\Form */
		$form->setBindOnValidate(false);
		$form->bind($team);
		
        /** @var $request \Zend\Http\Request */
        $request = $this->getRequest();
		if ($request->isPost()) {
			$data = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			$form->setData($data);
			if ($form->isValid()) {
				$form->bindValues();
				$this->getObjectManager()->flush();
				return true;
			}
        }
		return false;
	}
	
	protected function addSuccess($format, $params){
		$this->flashMessenger()->addSuccessMessage(vsprintf(
			$this->getTranslator()->translate($format),
			$params
		));
	}
	
	public function getTournamentManager() {
		return $this->tournamentManager;
	}

	public function getApiDataManager() {
		return $this->apiDataManager;
	}
	
	public function getTeamdataManager() {
		return $this->teamdataManager;
	}
	
	public function getTeamMatcher() {
		return $this->teamMatcher;
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
	
	public function setTeamMatcher(TeamMatcher $teamMatcher) {
		$this->teamMatcher = $teamMatcher;
		return $this;
	}
	
}
