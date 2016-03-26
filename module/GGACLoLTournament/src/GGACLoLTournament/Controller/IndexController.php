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

namespace GGACLoLTournament\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use GGACTournament\Tournament\ProviderAwareInterface;
use GGACTournament\Tournament\ProviderAwareTrait;

class IndexController extends AbstractActionController implements ProviderAwareInterface{
	
	use ProviderAwareTrait;
	public function indexAction(){
		return new ViewModel(array(
			'position' => 'ggac_index',
			'tournament' => $this->getTournamentProvider()->getTournament(),
		));
	}
	
	public function kontaktAction(){
		return new ViewModel(array(
			'position' => 'ggac_kontakt',
		));
	}
	
	public function infoAction(){
		$tournament = $this->getTournamentProvider()->getTournament();
		return new ViewModel(array(
			'position' => 'ggac_info_'.$tournament->getId(),
			'tournament' => $tournament,
		));
	}
	
	public function authenticateAction(){
		$this->authenticate();
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			if(!empty($data['redirect'])){
				$this->redirect()->toRoute($data['redirect']);
			}
		}
		return $this->redirect()->toRoute('home');
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