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

use GGACTournament\Tournament\Acl;

/**
 * Description of TournamentAcl
 *
 * @author schurix
 */
class TournamentAcl extends AbstractHelper{
	/** @var Acl */
	protected $acl;
	
	protected function getAcl() {
		return $this->acl;
	}

	public function setAcl(Acl $acl) {
		$this->acl = $acl;
		return $this;
	}

	public function __invoke($ressource = null, $team = null) {
		if($ressource === null){
			return $this;
		}
		return $this->isAllowed($ressource, $team);
	}
	
	public function isAllowed($ressource, $team = null){
		return $this->getAcl()->isAllowed($ressource, $team);
	}
}
