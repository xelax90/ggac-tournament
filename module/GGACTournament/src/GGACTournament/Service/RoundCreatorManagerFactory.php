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
use Zend\Mvc\Service\AbstractPluginManagerFactory;
use GGACTournament\Tournament\RoundCreator\Manager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Description of RoundCreatorManagerFactory
 *
 * @author schurix
 */
class RoundCreatorManagerFactory extends AbstractPluginManagerFactory{
	const PLUGIN_MANAGER_CLASS = Manager::class;
	
	public function __invoke(ContainerInterface $container, $name, array $options = null) {
		/* @var $manager Manager */
		$manager = parent::__invoke($container, $name, $options);
		
		$services = $container;
		if($services instanceof AbstractPluginManager){
			$services = $services->getServiceLocator();
		}
		
		$formManager = $services->get('FormElementManager');
		$manager->setFormManager($formManager);
		return $manager;
	}
}
