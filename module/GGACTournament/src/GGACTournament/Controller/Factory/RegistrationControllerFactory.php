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

namespace GGACTournament\Controller\Factory;
use GGACTournament\Tournament\Registration\Manager;
use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Description of RegistrationControllerFactory
 *
 * @author schurix
 */
class RegistrationControllerFactory extends AbstractTournamentControllerFactory{
	public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
		/* @var $controller \GGACTournament\Controller\RegistrationController */
		$controller = parent::__invoke($container, $requestedName, $options);
		
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		$registrationManager = $services->get(Manager::class);
		$apiManager = $services->get(ApiDataManager::class);
		$controller->setRegistrationManager($registrationManager);
		$controller->setApiDataManager($apiManager);
		
		return $controller;
	}
}
