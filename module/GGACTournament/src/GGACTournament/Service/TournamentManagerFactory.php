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

use GGACTournament\Tournament\Manager;
use GGACTournament\Tournament\Teamdata\Manager as TeamdataManager;
use GGACTournament\Tournament\Teamdata\TieBreak\Manager as TieBreakManager;
use GGACTournament\Tournament\ApiData\Manager as ApiDataManager;

/**
 * Description of TournamentManagerFactory
 *
 * @author schurix
 */
class TournamentManagerFactory extends TournamentServiceFactory {
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		// Create manager with injected TournamentManager
		/* @var $manager Manager */
		$manager = parent::__invoke($container, $requestedName, $options);
		
		// Fetch required managers
		$dataManager = $container->get(TeamdataManager::class);
		$tiebreakManager = $container->get(TieBreakManager::class);
		$apiManager = $container->get(ApiDataManager::class);
		
		// Inject managers
		$manager->setTeamdataManager($dataManager);
		$manager->setTieBreakManager($tiebreakManager);
		$manager->setApiDataManager($apiManager);
		
		return $manager;
	}
}
