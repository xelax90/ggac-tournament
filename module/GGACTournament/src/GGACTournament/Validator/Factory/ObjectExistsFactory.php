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

namespace GGACTournament\Validator\Factory;

use SkelletonApplication\Service\Factory\InvokableFactory;
use Interop\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\AbstractPluginManager;
/**
 * Description of ObjectExistsFactory
 *
 * @author schurix
 */
class ObjectExistsFactory extends InvokableFactory{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		$services = $container;
		if($container instanceof AbstractPluginManager){
			$services = $container->getServiceLocator();
		}
		if(!isset($options['object_repository']) && isset($options['entity_class'])){
			/* @var $em EntityManager */
			$em = $services->get(EntityManager::class);
			$options['object_repository'] = $em->getRepository($options['entity_class']);
		}
		
		return parent::__invoke($container, $requestedName, $options);
	}
}
