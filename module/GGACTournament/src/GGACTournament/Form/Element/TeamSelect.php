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

namespace GGACTournament\Form\Element;

use DoctrineModule\Form\Element\ObjectSelect;
use GGACTournament\Tournament\ProviderAwareInterface;
use GGACTournament\Tournament\ProviderAwareTrait;
use GGACTournament\Entity\Team;

/**
 * Description of TeamSelect
 *
 * @author schurix
 */
class TeamSelect extends ObjectSelect implements ProviderAwareInterface{
	use ProviderAwareTrait;
	
	public function setDefaultOptions(){
		$this->setOptions(array(
			'target_class'   => Team::class,
			'label_generator' => function($team) {
				return $team->getName();
			},
			'label' => gettext_noop('Team'),
			'find_method' => array(
				'name'   => 'getNotGroupedTeamsForTournament',
				'params' => array(
					'tournament' => $this->getTournamentProvider()->getTournament()
				),
			)
		));
	}
}
