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

use GGACTournament\Tournament\ProviderAwareInterface;
use GGACTournament\Tournament\ProviderAwareTrait;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\FormElementManager;
use ZfcUser\Form\Login;
use Zend\I18n\Translator\TranslatorInterface;


/**
 * Description of AbstractTournamentController
 *
 * @author schurix
 */
class AbstractTournamentController extends AbstractActionController implements ProviderAwareInterface, ObjectManagerAwareInterface{
	use ProvidesObjectManager, ProviderAwareTrait;
	
	/** @var FormElementManager */
	protected $formManager;
	
	/** @var Login */
	protected $loginForm;
	
	/** @var TranslatorInterface */
	protected $translator = null;
	
	protected function getFormManager() {
		return $this->formManager;
	}

	public function setFormManager(FormElementManager $formManager) {
		$this->formManager = $formManager;
		return $this;
	}
	
	protected function getLoginForm() {
		$loginForm = $this->loginForm;
		if(!$loginForm){
			return $loginForm;
		}
		$fm = $this->flashMessenger()->setNamespace('zfcuser-login-form')->getMessages();
		if (isset($fm[0])) {
			$loginForm->setMessages(
				array('identity' => array($fm[0]))
			);
		}
		return $loginForm;
	}

	public function setLoginForm(Login $loginForm) {
		$this->loginForm = $loginForm;
		return $this;
	}
	
	protected function getTranslator() {
		return $this->translator;
	}

	public function setTranslator(TranslatorInterface $translator) {
		$this->translator = $translator;
		return $this;
	}

	protected function authenticate(){
		if($this->zfcUserAuthentication()->hasIdentity()){
			return true;
		}
		
        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        $result = $adapter->prepareForAuthentication($this->getRequest());
        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
		return $auth;
	}
}
