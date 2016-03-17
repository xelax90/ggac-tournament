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

namespace GGACTournament\Tournament\Teamdata\TieBreak;

use RuntimeException;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;

use GGACTournament\Tournament\Teamdata\TieBreak\Factory\TieBreakFactory;

/**
 * Description of TieBreakManager
 *
 * @author schurix
 */
class Manager extends AbstractPluginManager{
	protected $instanceOf = ScoreInterface::class;
	
	protected $factories = [
		BuchholzScore::class => TieBreakFactory::class,
	];
	
	protected $scores;
	
	public function __construct($configOrContainerInstance = null, array $v3config = array()) {
		parent::__construct($configOrContainerInstance, $v3config);
		$this->canonicalNames[BuchholzScore::class] = BuchholzScore::class;
	}
	
    public function validate($instance){
		if (! $instance instanceof $this->instanceOf) {
			throw new InvalidServiceException(sprintf(
				'Invalid plugin "%s" created; not an instance of %s',
				get_class($instance),
				$this->instanceOf
			));
		}
	}
	
    public function validatePlugin($instance){
		try {
			$this->validate($instance);
		} catch (InvalidServiceException $e) {
			throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	public function getScores(){
		if(null === $this->scores){
			$scores = array();
			$services = $this->getRegisteredServices();
			foreach ($services as $type => $classes) {
				foreach ($classes as $class) {
					if(class_exists($class) && is_subclass_of($class, ScoreInterface::class)){
						$scores[$class] = substr($class, strrpos($class, '\\') + 1);
					}
				}
			}
			$this->scores = $scores;
		}
		return $this->scores;
	}
	
}
