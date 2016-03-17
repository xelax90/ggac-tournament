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

namespace GGACTournament\Tournament\Teamdata\TieBreak\Factory;

use Interop\Container\ContainerInterface;
use SkelletonApplication\Service\Factory\InvokableFactory;
use GGACTournament\Tournament\Teamdata\Manager;
use Zend\ServiceManager\AbstractPluginManager;
use GGACTournament\Tournament\Provider;

/**
 * Description of TieBreakFactory
 *
 * @author schurix
 */
class TieBreakFactory extends InvokableFactory{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		/* @var $scoreInstance \GGACTournament\Tournament\Teamdata\TieBreak\AbstractScore */
		$scoreInstance = parent::__invoke($container, $requestedName, $options);
		
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		// retrieve and inject dependencies
		$teamdataManager = $services->get(Manager::class);
		$provider = $services->get(Provider::class);
		$scoreInstance->setTeamdataManager($teamdataManager);
		$scoreInstance->setTournamentProvider($provider);
		
		return $scoreInstance;
	}
}
