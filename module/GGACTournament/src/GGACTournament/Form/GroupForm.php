<?php
namespace GGACTournament\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\InputFilter\InputFilter;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

/**
 * Group Form
 */
class GroupForm extends Form{

	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('GroupForm', $options);
		$this->setAttribute('method', 'post');
	}

	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());

		$this->add(array(
			'name' => 'group',
			'type' => GroupFieldset::class,
			'options' => array(
				'use_as_base_fieldset' => true,
			),
		));
		
		$this->add(array(
			'name' => 'group_csrf',
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
}
