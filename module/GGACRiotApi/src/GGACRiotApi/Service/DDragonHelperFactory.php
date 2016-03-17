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

use SkelletonApplication\Service\Factory\InvokableFactory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Description of DDragonHelperFactory
 *
 * @author schurix
 */
class DDragonHelperFactory extends InvokableFactory{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		/* @var $helper \FSMPILoL\View\Helper\DDragonHelper */
		$helper = parent::__invoke($container, $requestedName, $options);
		
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		/* @var $api Client */
		$api = $services->get(Client::class);
		$realm = $api->getRealm();
		$version = null;
		$cdn = null;
		if(!is_numeric($realm)){
			$data = json_decode($realm);
			$version = $data->dd;
			$cdn = $data->cdn;
		}
		
		if($version){
			$helper->setVersion($version);
		}
		if($cdn){
			$helper->setBaseUrl($cdn);
		}
		
		return $helper;
	}
}
