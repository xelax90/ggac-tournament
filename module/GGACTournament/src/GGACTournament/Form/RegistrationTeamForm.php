<?php
namespace GGACTournament\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;
use Zend\Form\Element\Collection;
use GGACTournament\Validator\NoObjectExistsInTournament;
use GGACTournament\Entity\Registration;
use GGACTournament\Validator\MinMaxEmailsRwth;
use GGACTournament\Validator\MinMaxEmailsNotRWTH;

/**
 * Registration Form
 */
class RegistrationTeamForm extends Form implements InputFilterProviderInterface{
	
	protected $minNotRwth = 0;
	
	protected $maxNotRwth = 3;
	
	protected $minRwth = 1;
	
	protected $maxRwth = PHP_INT_MAX;
	
	public function getMinNotRwth() {
		return $this->minNotRwth;
	}

	public function getMaxNotRwth() {
		return $this->maxNotRwth;
	}

	public function getMinRwth() {
		return $this->minRwth;
	}

	public function getMaxRwth() {
		return $this->maxRwth;
	}

	public function setMinNotRwth($minNotRwth) {
		$this->minNotRwth = $minNotRwth;
		return $this;
	}

	public function setMaxNotRwth($maxNotRwth) {
		$this->maxNotRwth = $maxNotRwth;
		return $this;
	}

	public function setMinRwth($minRwth) {
		$this->minRwth = $minRwth;
		return $this;
	}

	public function setMaxRwth($maxRwth) {
		$this->maxRwth = $maxRwth;
		return $this;
	}

	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('RegistrationSingleForm', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function setOptions($options) {
		parent::setOptions($options);
		
		if(isset($options['min_not_rwth'])){
			$this->setMinNotRwth($options['min_not_rwth']);
		}
		
		if(isset($options['max_not_rwth'])){
			$this->setMaxNotRwth($options['max_not_rwth']);
		}
		
		if(isset($options['min_rwth'])){
			$this->setMinRwth($options['min_rwth']);
		}
		
		if(isset($options['max_rwth'])){
			$this->setMaxRwth($options['max_rwth']);
		}
	}

	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'teamName',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Team Name<sup>*</sup>'),
				'label_options' => array(
					'disable_html_escape' => true,
				),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
			)
		));
		
		$this->add(array(
			'name' => 'team_icon_text',
			'type' => 'Hidden',
			'attributes' => array(
				'id' => 'team_icon_text',
			),
		));
		
		$this->add(array(
			'name' => 'registrations',
			'type' => Collection::class,
			'options' => array(
				'label' => gettext_noop('Spieler'),
				'count' => 2,
                'should_create_template' => true,
		        'template_placeholder' => '__placeholder__',
                'allow_add' => true,
                'target_element' => array(
					'type' => RegistrationFieldset::class,
					'options' => array(
						'show_isSub' => false,
						'show_anmerkung' => false,
						'data_required' => false,
					),
				),
			),
			'attributes' => array(
				'id' => "teamanmeldung_spieler",
			)
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
		
		$this->add(array(
			'name' => 'anmerkung',
			'type' => 'Textarea',
			'options' => array(
				'label' => gettext_noop('Anmerkungen'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
			)
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
			'teamName' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
				'validators' => array(
					array(
						'name' => NoObjectExistsInTournament::class,
						'options' => array(
							'entity_class' => Registration::class,
							'fields' => 'teamName'
						)
					)
				)
			),
			
			'team_icon_text' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
				'validators' => array(
					array(
						'name' => NoObjectExistsInTournament::class,
						'options' => array(
							'entity_class' => Registration::class,
							'fields' => 'icon'
						),
					),
					array(
						'name' => MinMaxEmailsRwth::class,
						'options' => array(
							'min' => $this->getMinRwth(), 
							'max' => $this->getMaxRwth(),
							'messages'=> array(
								MinMaxEmailsRwth::MESSAGE_NOT_ENOUGH => 'Es muss mindestens '.$this->getMinRwth().' RWTH-Mailadresse(n) angegeben sein',
							)
						)
					),
					array(
						'name' => MinMaxEmailsNotRWTH::class,
						'options' => array(
							'min' => $this->getMinNotRwth(),
							'max' => $this->getMaxNotRwth(),
							'messages'=> array(
								MinMaxEmailsNotRWTH::MESSAGE_TOO_MANY => 'Es dürfen nicht mehr, als '.$this->getMaxNotRwth().' externe Mailadresse(n) angegeben sein',
							)
						)
					),
				),
			),
			
			'anmerkung' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
			),
			
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
