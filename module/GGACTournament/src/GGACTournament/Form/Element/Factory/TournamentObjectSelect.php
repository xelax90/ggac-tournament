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

namespace GGACTournament\Form\Element\Factory;

use GGACTournament\Tournament\Provider;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Description of TournamentObjectSelect
 *
 * @author schurix
 */
class TournamentObjectSelect extends GenericObjectSelectFactory{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
		$elm = parent::__invoke($container, $requestedName, $options);
		
		$service = $container;
		if($service instanceof AbstractPluginManager){
			$service = $service->getServiceLocator();
		}
		
		$provider = $service->get(Provider::class);
		$elm->setTournamentProvider($provider);
		$elm->setDefaultOptions();
		
		return $elm;
	}
}
