<?php
namespace GGACTournament\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\InputFilter\InputFilter;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

/**
 * GroupTeamMapping Form
 */
class GroupTeamMappingForm extends Form{

	/** @var boolean */
	protected $showTeamSelect = true;
	
	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('GroupTeamMappingForm', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function setOptions($options) {
		if(isset($options['show_team_select'])){
			$this->setShowTeamSelect($options['show_team_select']);
		}
		
		return parent::setOptions($options);
	}
	
	public function getShowTeamSelect() {
		return $this->showTeamSelect;
	}

	public function setShowTeamSelect($showTeamSelect) {
		$this->showTeamSelect = $showTeamSelect;
		if($this->has('groupteammapping')){
			$this->get('groupteammapping')->setShowTeamSelect($showTeamSelect);
		}
		return $this;
	}

	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());

		$this->add(array(
			'name' => 'groupteammapping',
			'type' => GroupTeamMappingFieldset::class,
			'options' => array(
				'use_as_base_fieldset' => true,
				'show_team_select' => $this->getShowTeamSelect(),
			),
		));
		
		$this->add(array(
			'name' => 'groupteammapping_csrf',
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
