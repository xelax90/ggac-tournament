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

namespace GGACTournament\Tournament\RoundCreator\Factory;

use Interop\Container\ContainerInterface;
use SkelletonApplication\Service\Factory\InvokableFactory;
use Doctrine\ORM\EntityManager;
use GGACTournament\Tournament\Teamdata\Manager as TeamdataManager;
use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Description of RoundCreatorFactory
 *
 * @author schurix
 */
class RoundCreatorFactory extends InvokableFactory{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		// Create requested service
		/* @var $roundCreator \GGACTournament\Tournament\RoundCreator\RoundCreatorInterface */
		$roundCreator = parent::__invoke($container, $requestedName, $options);
		
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		// retrieve and inject object manager
		$objectManager = $services->get(EntityManager::class);
		$roundCreator->setObjectManager($objectManager);
		
		// retrieve and inject teamdata manager
		$teamdataManager = $services->get(TeamdataManager::class);
		$roundCreator->setTeamdataManager($teamdataManager);
		
		// retrieve and inject api data manager
		$apiDataManager = $services->get(ApiDataManager::class);
		$roundCreator->setApiDataManager($apiDataManager);
		
		return $roundCreator;
	}
}
