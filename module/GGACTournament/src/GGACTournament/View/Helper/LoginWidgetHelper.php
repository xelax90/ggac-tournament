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

namespace GGACTournament\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ZfcUser\Form\Login;

class LoginWidgetHelper extends AbstractHelper{
	
	/** @var Login */
	protected $loginForm;
	
	/** @var string */
	protected $partial = 'partial/loginWidget';
	
	protected $redirect = null;
	
	protected $renderEmpty = true;
	
	protected $emptyPartial = 'partial/empty';
	
	public function __invoke($show = null) {
		if($show !== null){
			$this->setRenderEmpty(!$show);
		}
		return $this;
	}
	
	public function render(){
		$partial = $this->getPartial();
		if($this->getRenderEmpty()){
			$partial = $this->getEmptyPartial();
		}
		return $this->getView()->render($partial, array(
			'loginForm' => $this->getLoginForm(),
			'redirectRoute' => $this->getRedirect(),
		));
	}
	
	public function getLoginForm() {
		return $this->loginForm;
	}

	public function getPartial() {
		return $this->partial;
	}
	
	public function getRedirect() {
		return $this->redirect;
	}
	
	public function getRenderEmpty() {
		return $this->renderEmpty;
	}
	
	public function getEmptyPartial() {
		return $this->emptyPartial;
	}

	public function setLoginForm(Login $loginForm) {
		$this->loginForm = $loginForm;
		return $this;
	}

	public function setPartial($partial) {
		$this->partial = $partial;
		return $this;
	}

	public function setRedirect($redirect) {
		$this->redirect = $redirect;
		return $this;
	}

	public function setRenderEmpty($renderEmpty) {
		$this->renderEmpty = $renderEmpty;
		return $this;
	}
	
	public function setEmptyPartial($emptyPartial) {
		$this->emptyPartial = $emptyPartial;
		return $this;
	}

}