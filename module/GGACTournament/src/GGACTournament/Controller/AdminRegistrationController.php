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
use GGACTournament\Tournament\ProviderAwareInterface;
use GGACTournament\Tournament\ProviderAwareTrait;
use GGACTournament\Tournament\Registration\Manager as RegistrationManager;

/**
 * Description of AdminRegistrationController
 *
 * @author schurix
 */
class AdminRegistrationController extends ListController implements ProviderAwareInterface{
	use ProviderAwareTrait;
	
	/** @var RegistrationManager */
	protected $registrationManager;
	
	protected function getRegistrationManager() {
		return $this->registrationManager;
	}

	public function setRegistrationManager(RegistrationManager $registrationManager) {
		$this->registrationManager = $registrationManager;
		return $this;
	}

	protected function getAll() {
		return $this->getRegistrationManager()->getAll();
	}
	
	/**
	 * @param \GGACTournament\Entity\Registration $item
	 */
	protected function _preCreate($item) {
		parent::_preCreate($item);
		$item->setTournament($this->getTournamentProvider()->getTournament());
	}
	
}
