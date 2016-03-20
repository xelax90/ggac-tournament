<?php
namespace GGACTournament\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\InputFilter\InputFilter;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

/**
 * Registration Form
 */
class RegistrationForm extends Form{

	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('RegistrationForm', $options);
		$this->setAttribute('method', 'post');
	}

	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());

		$this->add(array(
			'name' => 'registration',
			'type' => RegistrationFieldset::class,
			'options' => array(
				'use_as_base_fieldset' => true,
				'show_teamName' => true,
				'data_required' => false,
			),
		));
		
		$this->add(array(
			'name' => 'registration_csrf',
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
