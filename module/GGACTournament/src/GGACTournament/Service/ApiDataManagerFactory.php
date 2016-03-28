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

use GGACTournament\Tournament\ApiData\Manager;
use GGACTournament\Tournament\ApiData\ApiInterface;
use GGACTournament\Tournament\ApiData\TournamentApiInterface;
use GGACTournament\Tournament\ApiData\Cache;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Factory for ApiData manager
 *
 * @author schurix
 */
class ApiDataManagerFactory extends TournamentServiceFactory {
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		// Create manager with injected TournamentManager
		/* @var $apiManager Manager */
		$apiManager = parent::__invoke($container, $requestedName, $options);
		
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		$apiCache = $services->get(Cache::class);
		$apiManager->setCache($apiCache);
		
		if($services->has(ApiInterface::class)){
			// fetch and inject API
			$api = $services->get(ApiInterface::class);
			$apiManager->setApi($api);
		}
		
		if($services->has(TournamentApiInterface::class)){
			// fetch and inject Tournament API
			$tournamentApi = $services->get(TournamentApiInterface::class);
			$apiManager->setTournamentApi($tournamentApi);
		}
		
		return $apiManager;
	}
}
