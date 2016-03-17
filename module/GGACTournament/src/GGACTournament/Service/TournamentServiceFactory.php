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
use SkelletonApplication\Service\Factory\InvokableFactory;
use GGACTournament\Tournament\Provider;
use GGACTournament\Tournament\ProviderAwareInterface;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use GGACTournament\Tournament\OptionsAwareInterface;
use Doctrine\ORM\EntityManager;
use GGACTournament\Options\TournamentOptions;

/**
 * Description of TournamentServcieFactory
 *
 * @author schurix
 */
class TournamentServiceFactory extends InvokableFactory{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		// Create requested service
		$tournamentService = parent::__invoke($container, $requestedName, $options);
		
		if($tournamentService instanceof OptionsAwareInterface){
			// Fetch and inject tournament options
			/* @var $tournamentOptions TournamentOptions */
			$tournamentOptions = $container->get(TournamentOptions::class);
			$tournamentService->setTournamentOptions($tournamentOptions);
		}
		
		if($tournamentService instanceof ProviderAwareInterface){
			// Fetch and inject tournament provider
			/* @var $provider Provider */
			$provider = $container->get(Provider::class);
			$tournamentService->setTournamentProvider($provider);
		}
		
		if($tournamentService instanceof ObjectManagerAwareInterface){
			// Fetch and inject entity manager
			/* @var $objectManager EntityManager */
			$objectManager = $container->get(EntityManager::class);
			$tournamentService->setObjectManager($objectManager);
		}
		
		return $tournamentService;
	}
}
