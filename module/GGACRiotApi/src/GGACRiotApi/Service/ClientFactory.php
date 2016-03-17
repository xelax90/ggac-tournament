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
use SkelletonApplication\Service\Factory\InvokableFactory;
use GGACRiotApi\Cache\ApiCache;
use GGACRiotApi\Options\ApiOptions;

/**
 * Description of ClientFactory
 *
 * @author schurix
 */
class ClientFactory extends InvokableFactory{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		/* @var $client Client */
		$client = parent::__invoke($container, $requestedName, $options);
		
		$cache = $container->get(ApiCache::class);
		$options = $container->get(ApiOptions::class);
		
		$client->setCache($cache);
		$client->setOptions($options);
		
		return $client;
	}
}
