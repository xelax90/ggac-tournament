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

namespace GGACTournament\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;
use Zend\Form\Element\Collection;
use GGACTournament\Entity\Match;

/**
 * MatchTimeForm
 */
class MatchTimeForm extends Form implements InputFilterProviderInterface{
	
	/** @var Match */
	protected $match;
	
	protected $isHome;
	
	public function __construct($name = "", $options = array()){
		if(is_array($name) && empty($options)){
			$options = $name;
		}
		// we want to ignore the name passed
		parent::__construct('MatchUserResultForm', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function getIsHome() {
		return $this->isHome;
	}

	public function setIsHome($isHome) {
		$this->isHome = $isHome;
		if($this->getBaseFieldset()){
			$this->getBaseFieldset()->setIsHome($isHome);
		}
		return $this;
	}

	public function setOptions($options) {
		parent::setOptions($options);
		
		if(isset($options['isHome'])){
			$this->setIsHome($options['isHome']);
		}
		return $this;
	}
	
	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());

		$this->add(array(
			'name' => 'match',
			'type' => MatchTimeFieldset::class,
			'options' => array(
				'use_as_base_fieldset' => true,
				'isHome' => $this->getIsHome(),
			),
		));
		
		/* $this->add(array(
			'name' => 'registration_csrf',
			'type' => Csrf::class,
		)); */

		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'value' => 'Save',
				'class' => 'btn-success'
			),
			'options' => array(
				'as-group' => true,
			)
		));
	}

	public function getInputFilterSpecification() {
		
		$filters = array(
		);
		
		return $filters;
	}

}
