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

namespace GGACRiotApi\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use SkelletonApplication\Service\Factory\InvokableFactory;
use GGACRiotApi\Options\ApiOptions;

/**
 * Description of ClientFactory
 *
 * @author schurix
 */
class ClientFactory extends InvokableFactory{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		/* @var $client TournamentClient */
		$client = parent::__invoke($container, $requestedName, $options);
		
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		$apiOptions = $services->get(ApiOptions::class);
		$router = $services->get('Router');
		
		$client->setRouter($router);
		$client->setOptions($apiOptions);
		
		return $client;
	}
}
