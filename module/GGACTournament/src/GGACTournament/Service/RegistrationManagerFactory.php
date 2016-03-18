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

namespace GGACTournament\Service;

use Interop\Container\ContainerInterface;

use GGACTournament\Tournament\Registration\Manager;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Description of TeamdataManagerFactory
 *
 * @author schurix
 */
class RegistrationManagerFactory extends TournamentServiceFactory {
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		// Create manager with injected TournamentManager
		/* @var $manager Manager */
		$manager = parent::__invoke($container, $requestedName, $options);
		
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		$formManager = $services->get('FormElementManager');
		$userMapper = $services->get('zfcuser_user_mapper');
		$userService = $services->get('zfcuser_user_service');
		$passwordService = $services->get('goalioforgotpassword_password_service');
		$generator = $services->get('XelaxPasswordGenerator\Default');
		$manager->setFormManager($formManager);
		$manager->setUserMapper($userMapper);
		$manager->setUserService($userService);
		$manager->setPasswordService($passwordService);
		$manager->setPasswordGenerator($generator);
		
		return $manager;
	}
}
