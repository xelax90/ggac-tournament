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
use GGACTournament\Tournament\Registration\Manager as RegistrationManager;
use GGACTournament\Form\RegistrationTeamForm;
use GGACTournament\Form\RegistrationSingleForm;
use Zend\View\Model\ViewModel;
use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use Zend\Form\Form;

/**
 * Description of RegistrationController
 *
 * @author schurix
 */
class RegistrationController extends AbstractTournamentController{
	
	/** @var RegistrationManager */
	protected $registrationManager;
	
	/** @var ApiDataManager */
	protected $apiDataManager;
	
	public function getRegistrationManager() {
		return $this->registrationManager;
	}
	
	/**
	 * @param RegistrationManager $registrationManager
	 * @return RegistrationController
	 */
	public function setRegistrationManager(RegistrationManager $registrationManager) {
		$this->registrationManager = $registrationManager;
		return $this;
	}
	
	/**
	 * @return ApiDataManager
	 */
	public function getApiDataManager() {
		return $this->apiDataManager;
	}
	
	/**
	 * @param ApiDataManager $apiDataManager
	 * @return RegistrationController
	 */
	public function setApiDataManager(ApiDataManager $apiDataManager) {
		$this->apiDataManager = $apiDataManager;
		return $this;
	}

	protected function _forwardToForm(){
		$match = $this->getEvent()->getRouteMatch()->getParams();
		$match['action'] = 'form';
		return $this->forward()->dispatch($match['controller'], $match);
	}
	
	protected function _forwardToConfirm(){
		$match = $this->getEvent()->getRouteMatch()->getParams();
		$match['action'] = 'confirm';
		return $this->forward()->dispatch($match['controller'], $match);
	}
	
	protected function _redirectToForm(){
		$match = $this->getEvent()->getRouteMatch()->getParams();
		$match['action'] = 'confirm';
		return $this->redirect()->toRoute('registration/form');
	}
	
	protected function getFormOrRedirect(){
		$request = $this->getRequest();
		
		if(!$request->isPost()){
			return $this->_redirectToForm();
		}
		
		$data = $request->getPost();
		if($this->getRegistrationManager()->dataIsTeam($data)){
			$form = $this->getRegistrationManager()->getTeamForm();
		} elseif($this->getRegistrationManager()->dataIsSingle($data)){
			$form = $this->getRegistrationManager()->getSingleForm();
		} else {
			return $this->_redirectToForm();
		}
		$form->setData($data);
		if(!$form->isValid()){
			return $this->_forwardToForm();
		}
		return $form;
	}
	
	public function indexAction() {
		return $this->_redirectToForm();
	}
	
	public function formAction(){
		$this->authenticate(); // handle login form
		$request = $this->getRequest();
		
		$singleForm = $this->getRegistrationManager()->getSingleForm();
		$teamForm = $this->getRegistrationManager()->getTeamForm();
		
		$data = array();
		if($request->isPost()){
			$data = $request->getPost();
		}
		if(
			// if user is logged in and no valid request is sent
			$this->zfcUserAuthentication()->hasIdentity() &&
			( !$request->isPost() || (
				!$this->getRegistrationManager()->dataIsTeam($data) && 
				!$this->getRegistrationManager()->dataIsSingle($data)
			))
		){
			// prefill forms with known data
			$this->getRegistrationManager()->prefillForm($this->zfcUserAuthentication()->getIdentity(), $singleForm);
			$this->getRegistrationManager()->prefillForm($this->zfcUserAuthentication()->getIdentity(), $teamForm);
		}
		
		if(
			// if request is valid
			$request->isPost() && (
				$this->getRegistrationManager()->dataIsSingle($data) ||
				$this->getRegistrationManager()->dataIsTeam($data)
			)
		){
			if($this->getRegistrationManager()->dataIsTeam($data)){
				$currentForm = $teamForm;
			} else {
				$currentForm = $singleForm;
			}
			if($currentForm){
				$currentForm->setData($data);
				if($currentForm->isValid()){
					return $this->_forwardToConfirm();
				}
			}
		}
		$icons = $this->getRegistrationManager()->getAvailableIcons();
		$loginForm = $this->getLoginForm();
		
		return new ViewModel(array(
			'singleForm' => $singleForm,
			'teamForm' => $teamForm,
			'icons' => $icons,
			'loginForm' => $loginForm,
			'tournament' => $this->getTournamentProvider()->getTournament(),
		));
	}
	
	public function confirmAction(){
		$formOrRedirect = $this->getFormOrRedirect();
		$this->getResponse();
		if(! ($formOrRedirect instanceof Form)){
			return $formOrRedirect;
		}
		$form = $formOrRedirect;
		
		$icons = $this->getRegistrationManager()->getAvailableIcons();
		
		$loginForm = $this->getLoginForm();
		
		return new ViewModel(array(
			'form' => $form, 
			'icons' => $icons, 
			'loginForm' => $loginForm,
			'tournament' => $this->getTournamentProvider()->getTournament(),
		));
	}
	
	public function readyAction(){
		$loginForm = $this->getLoginForm();
		
		$params = array(
			'loginForm' => $loginForm,
			'tournament' => $this->getTournamentProvider()->getTournament(),
		);
		
		// Run validation and get the used form
		$formOrRedirect = $this->getFormOrRedirect();
		if(! ($formOrRedirect instanceof Form)){
			return $formOrRedirect;
		}
		$saveResult = $this->getRegistrationManager()->saveForm($formOrRedirect);
		if(!$saveResult){
			return $this->_redirectToForm();
		}
		if($formOrRedirect instanceof RegistrationTeamForm){
			$params['team'] = $saveResult;
		} elseif($formOrRedirect instanceof RegistrationSingleForm) {
			$params['registration'] = $saveResult;
		} else {
			$this->flashMessenger()->addErrorMessage('Bei der Anmeldung ist ein Fehler aufgetreten. Bitte versuche es erneut oder kontaktiere die Turnierleitung.');
			return $this->_redirectToForm();
		}
		
		return new ViewModel($params);
	}
	
	public function displayAction(){
		$this->getApiDataManager()->setData();
		
		$singles = $this->getRegistrationManager()->getSingles();
		$teams = $this->getRegistrationManager()->getTeams();
		$loginForm = $this->getLoginForm();
		
		return new ViewModel(array(
			'singles' => $singles,
			'teams' => $teams,
			'loginForm' => $loginForm,
		));
	}
}
