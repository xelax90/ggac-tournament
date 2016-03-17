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

use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use GGACTournament\Tournament\Registration\Manager as RegistrationManager;

/**
 * Description of TeamMatcherFactory
 *
 * @author schurix
 */
class TeamMatcherFactory extends TournamentServiceFactory {
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		/* @var $service \GGACTournament\Tournament\TeamMatcher\TeamMatcher */
		$service = parent::__invoke($container, $requestedName, $options);
		
		$apiManager = $container->get(ApiDataManager::class);
		$registrationManager = $container->get(RegistrationManager::class);
		
		$service->setApiDataManager($apiManager);
		$service->setRegistrationManager($registrationManager);
		
		return $service;
	}
}
