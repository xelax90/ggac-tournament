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
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use GGACRiotApi\Cache\TournamentReport;
use Zend\Cache\StorageFactory;

/**
 * Description of CacheFactory
 *
 * @author schurix
 */
class TournamentCacheFactory implements FactoryInterface {
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		$cache = StorageFactory::factory(array(
			'adapter' => array(
				'name' => TournamentReport::class,
				'options' => array(
					'ttl' => 604800, // 7 days
					'namespace' => 'riotgamereport',
					'cache_dir' => './data/cache/',
				),
			),
			'plugins' => array(
				'exception_handler' => array('throw_exceptions' => false),
			),
		));
		return $cache;
	}

	public function createService(ServiceLocatorInterface $services) {
		return $this($services, TeamdataCache::class);
	}
}
