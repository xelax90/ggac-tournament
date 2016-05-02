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

namespace GGACTournament\Tournament\RoundCreator;

use RuntimeException;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\Form\FormElementManager;

use GGACTournament\Entity\Group;
use GGACTournament\Form\RoundForm;
use GGACTournament\Tournament\RoundCreator\Factory\RoundCreatorFactory;

/**
 * Description of PluginManager
 *
 * @author schurix
 */
class Manager extends AbstractPluginManager{
	/** @var FormElementManager */
	protected $formManager;
	
	protected function getFormManager() {
		return $this->formManager;
	}

	public function setFormManager(FormElementManager $formManager) {
		$this->formManager = $formManager;
		return $this;
	}
	
	protected $instanceOf = RoundCreatorInterface::class;
	
	protected $factories = [
		RandomRoundCreator::class => RoundCreatorFactory::class,
		SwissRoundCreator::class => RoundCreatorFactory::class,
	];
	
	protected $roundTypes;
	
	public function __construct($configOrContainerInstance = null, array $v3config = array()) {
		parent::__construct($configOrContainerInstance, $v3config);
		$this->canonicalNames[RandomRoundCreator::class] = RandomRoundCreator::class;
		$this->canonicalNames[SwissRoundCreator::class] = SwissRoundCreator::class;
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
	
	public function nextRound($type, Group $group, RoundConfig $roundConfig, AlreadyPlayedInterface $gameCheck){
		if(!$this->has($type)){
			return false;
		}
		
		/* @var $creator RoundCreatorInterface */
		$creator = $this->get($type);
		$creator->nextRound($group, $roundConfig, $gameCheck);
		return true;
	}
	
	public function getRoundTypes(){
		if(null === $this->roundTypes){
			$roundTypes = array();
			$services = $this->getRegisteredServices();
			foreach ($services as $type => $classes) {
				foreach ($classes as $class) {
					if(class_exists($class) && is_subclass_of($class, RoundCreatorInterface::class)){
						$roundTypes[$class] = substr($class, strrpos($class, '\\') + 1);
					}
				}
			}
			$this->roundTypes = $roundTypes;
		}
		return $this->roundTypes;
	}
	
	/**
	 * @return RoundForm
	 */
	public function getCreateForm(){
		$form = $this->getFormManager()->get(RoundForm::class);
		return $form;
	}
	
	/**
	 * @return RoundForm
	 */
	public function getEditForm(){
		$form = $this->getFormManager()->get(RoundForm::class, array(
			'show_type' => false,
			'show_game_count' => false,
		));
		return $form;
	}
	
}
