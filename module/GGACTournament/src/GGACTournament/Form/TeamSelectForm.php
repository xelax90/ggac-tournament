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
use GGACTournament\Form\Element\TeamSelect;

/**
 * Select team form
 */
class TeamSelectForm extends Form implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		if(is_array($name) && empty($options)){
			$options = $name;
		}
		// we want to ignore the name passed
		parent::__construct('MatchCommentForm', $options);
		$this->setAttribute('method', 'post');
	}

	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'team',
			'type' => TeamSelect::class,
			'options' => array(
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'data-fancy' => "1",
			)
		));
		
		$this->add(array(
			'name' => 'matchcomment_csrf',
			'type' => Csrf::class,
		));

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
			'team' => array(
				'required' => true,
			),
		);
		return $filters;
	}

}
