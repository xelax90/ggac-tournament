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

namespace GGACLoLTournament\Navigation;

use Zend\Navigation\Service\AbstractNavigationFactory;
use GGACTournament\Tournament\ProviderAwareTrait;
use GGACTournament\Tournament\Provider;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Description of TournamentNavigationFactory
 *
 * @author schurix
 */
class TournamentNavigationFactory extends AbstractNavigationFactory{
	use ProviderAwareTrait;
	
	public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		$this->setTournamentProvider($services->get(Provider::class));
		return parent::__invoke($container, $requestedName, $options);
	}
	
	protected function getName() {
		return 'tournament';
	}
	
	protected function getPagesFromConfig($config = null) {
		$pages = parent::getPagesFromConfig($config);
		$filtered = array();
		$currentPhase = $this->getTournamentProvider()->getTournament()->getCurrentPhase();
		foreach($pages as $page){
			if(isset($page['tournamentState']) && $page['tournamentState'] != $currentPhase->getTournamentState()){
				continue;
			}
			$filtered[] = $page;
		}
		return $filtered;
	}
}
