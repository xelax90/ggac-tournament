<?php
namespace GGACTournament\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Registration Form
 */
class RegistrationSingleForm extends Form implements InputFilterProviderInterface{

	protected $requireRwth = true;
	
	protected $subOnly = false;
	
	public function __construct($name = "", $options = array()){
		if(is_array($name) && empty($options)){
			$options = $name;
		}
		// we want to ignore the name passed
		parent::__construct('RegistrationSingleForm', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function getRequireRwth() {
		return $this->requireRwth;
	}

	public function setRequireRwth($requireRwth) {
		$this->requireRwth = $requireRwth;
		return $this;
	}
	
	public function getSubOnly() {
		return $this->subOnly;
	}

	public function setSubOnly($subOnly) {
		$this->subOnly = $subOnly;
		return $this;
	}

	public function setOptions($options) {
		parent::setOptions($options);
		
		if(isset($options['require_rwth'])){
			$this->setRequireRwth($options['require_rwth']);
		}
		
		if(isset($options['sub_only'])){
			$this->setSubOnly($options['sub_only']);
			if($this->has('registration')){
				$this->get('registration')->setSubOnly($this->getSubOnly());
			}
		}
	}
	
	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());

		$this->add(array(
			'name' => 'registration',
			'type' => RegistrationFieldset::class,
			'options' => array(
				'use_as_base_fieldset' => true,
				'require_rwth' => $this->getRequireRwth(),
				'show_teamName' => false,
				'sub_only' => $this->getSubOnly(),
			),
		));
		
		$this->add(array(
			'name' => 'ausschreibung_gelesen',
			'type' => 'Checkbox',
			'options' => array(
				'label' => gettext_noop('Ausschreibung gelesen<sup>*</sup>'),
				'checked_value' => '1',
				'label_options' => array(
					'disable_html_escape' => true,
				),
				'column-size' => 'sm-10 col-sm-offset-2',
			),
			'attributes' => array(
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
			'ausschreibung_gelesen' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'Digits'),
				),
				'validators' => array(
					array(
						'name' => 'Callback',
						'options' => array(
							'callback' => function($v){
								return !empty($v);
							},
							'message' => 'Du musst die Ausschreibung lesen, um am Turnier teilzunehmen',
						)
					)
				)
			),
		);
		
		return $filters;
	}

}
