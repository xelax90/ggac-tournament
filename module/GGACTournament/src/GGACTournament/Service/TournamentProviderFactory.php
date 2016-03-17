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

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use GGACTournament\Options\TournamentOptions;
use Doctrine\ORM\EntityManager;
use GGACTournament\Entity\Tournament;
use GGACTournament\Tournament\Provider;

use GGACTournament\Tournament\Exception\TournamentNotFoundException;

/**
 * Creates tournament provider with default tournament
 *
 * @author schurix
 */
class TournamentProviderFactory implements FactoryInterface {
	public function createService(ServiceLocatorInterface $serviceLocator) {
		/* @var $options TournamentOptions */
		$options = $serviceLocator->get(TournamentOptions::class);
		
		$tournament = null;
		if(!empty($options->getDefaultTournamentId())){
			/* @var $em EntityManager */
			$em = $serviceLocator->get(EntityManager::class);
			$tournament = $em->getRepository(Tournament::class)->find($options->getDefaultTournamentId());
		}
		
		if(!$tournament){
			throw new TournamentNotFoundException();
		}
		
		$provider = new Provider($tournament);
		return $provider;
	}
}
