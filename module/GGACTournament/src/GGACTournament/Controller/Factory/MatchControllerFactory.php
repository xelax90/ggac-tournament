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
use Zend\ServiceManager\AbstractPluginManager;
use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;
use GGACTournament\Tournament\Manager as TournamentManager;
use GGACTournament\Tournament\Teamdata\Manager as TeamdataManager;

/**
 * Description of MatchControllerFactory
 *
 * @author schurix
 */
class MatchControllerFactory extends AbstractTournamentControllerFactory{
	public function __invoke(\Interop\Container\ContainerInterface $container, $requestedName, array $options = null) {
		/* @var $controller \GGACTournament\Controller\MatchController */
		$controller = parent::__invoke($container, $requestedName, $options);
		
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		$apiManager = $services->get(ApiDataManager::class);
		$teamdataManager = $services->get(TeamdataManager::class);
		$tournamentManager = $services->get(TournamentManager::class);
		$controller->setApiDataManager($apiManager);
		$controller->setTeamdataManager($teamdataManager);
		$controller->setTournamentManager($tournamentManager);
		
		return $controller;
	}
}
