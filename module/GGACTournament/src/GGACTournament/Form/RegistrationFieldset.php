<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

use GGACTournament\Entity\Registration;
use GGACTournament\Validator\UniqueObjectInTournament;
use GGACTournament\Validator\EmailIsRwth;
use GGACTournament\Validator\EmailIsNotTim;
use Zend\Form\Element\Select;
/**
 * Registration Fieldset
 */
class RegistrationFieldset extends Fieldset implements InputFilterProviderInterface{
	
	/** @var boolean */
	protected $dataRequired = true;
	
	/** @var boolean */
	protected $requireRWTH = false;
	
	/** @var boolean */
	protected $showIsSub = true;
	
	/** @var boolean */
	protected $showAnmerkung = true;
	
	/** @var boolean */
	protected $showTeamName = true;
	
	/** @var boolean */
	protected $subOnly = false;
	
	protected $isSubOptions = array(
		'-1' => 'Bitte Wählen', 
		'2' => 'Ja, ich möchte NUR Ersatzspieler sein', 
		'1' => 'Ja, ich könnte auch Ersatzspieler sein', 
		'0' => "Nein, ich möchte kein Ersatzspieler sein."
	);
	
	public function __construct($name = "", $options = array()){
		if(is_array($name) && empty($options)){
			$options = $name;
			$name = "";
		}
		if($name == ""){
			$name = 'RegistrationFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function setOptions($options) {
		parent::setOptions($options);
        if (isset($options['data_required'])) {
            $this->setDataRequired($options['data_required']);
        }
		
        if (isset($options['require_rwth'])) {
            $this->setRequireRWTH($options['require_rwth']);
        }
		
        if (isset($options['show_isSub'])) {
            $this->setShowIsSub($options['show_isSub']);
        }
		
        if (isset($options['show_anmerkung'])) {
            $this->setShowAnmerkung($options['show_anmerkung']);
        }
		
        if (isset($options['show_teamName'])) {
            $this->setShowTeamName($options['show_teamName']);
        }
		
        if (isset($options['sub_only'])) {
            $this->setSubOnly($options['sub_only']);
        }
	}
	
	/**
	 * @return boolean
	 */
	public function getDataRequired() {
		return $this->dataRequired;
	}
	
	/**
	 * @return boolean
	 */
	public function getRequireRWTH() {
		return $this->requireRWTH;
	}
	
	/**
	 * @return boolean
	 */
	public function getShowIsSub() {
		return $this->showIsSub;
	}

	/**
	 * @return boolean
	 */
	public function getShowAnmerkung() {
		return $this->showAnmerkung;
	}
	
	/**
	 * @return boolean
	 */
	public function getShowTeamName() {
		return $this->showTeamName;
	}
	
	/**
	 * @return boolean
	 */
	public function getSubOnly() {
		return $this->subOnly;
	}

	/**
	 * @param boolean $dataRequired
	 * @return RegistrationFieldset
	 */
	public function setDataRequired($dataRequired) {
		$this->dataRequired = $dataRequired;
		return $this;
	}

	/**
	 * @param boolean $requireRWTH
	 * @return RegistrationFieldset
	 */
	public function setRequireRWTH($requireRWTH) {
		$this->requireRWTH = $requireRWTH;
		return $this;
	}
	
	/**
	 * @param boolean $showIsSub
	 * @return RegistrationFieldset
	 */
	public function setShowIsSub($showIsSub) {
		$this->showIsSub = $showIsSub;
		if(!$showIsSub && $this->has('isSub')){
			$this->remove('isSub');
		}
		return $this;
	}

	/**
	 * @param boolean $showAnmerkung
	 * @return RegistrationFieldset
	 */
	public function setShowAnmerkung($showAnmerkung) {
		$this->showAnmerkung = $showAnmerkung;
		if(!$showAnmerkung && $this->has('anmerkung')){
			$this->remove('anmerkung');
		}
		return $this;
	}
	
	/**
	 * @param boolean $showTeamName
	 * @return RegistrationFieldset
	 */
	public function setShowTeamName($showTeamName) {
		$this->showTeamName = $showTeamName;
		if(!$showTeamName && $this->has('teamName')){
			$this->remove('teamName');
		}
		return $this;
	}
	
	/**
	 * @param boolean $subOnly
	 * @return RegistrationFieldset
	 */
	public function setSubOnly($subOnly) {
		$this->subOnly = $subOnly;
		if($this->has('isSub')){
			$options = $this->get('isSub')->getOptions();
			if($subOnly){
				$options['options'] = array('2' => $this->isSubOptions['2']);
			} else {
				$options['options'] = $this->isSubOptions;
			}
			$this->get('isSub')->setOptions($options);
		}
		return $this;
	}

	public function init(){
		parent::init();
		$this->setObject(new Registration());
		
		$this->add(array(
			'name' => 'id',
			'type' => 'Hidden',
		));
		
		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Name<sup>*</sup>'),
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
		
		if($this->getShowTeamName()){
			$this->add(array(
				'name' => 'teamName',
				'type' => 'Text',
				'options' => array(
					'label' => gettext_noop('Team Name'),
					'column-size' => 'sm-10',
					'label_attributes' => array(
						'class' => 'col-sm-2',
					),
				),
				'attributes' => array(
					'id' => "",
				)
			));
		}


		$this->add(array(
			'name' => 'email',
			'type' => 'Email',
			'options' => array(
				'label' => gettext_noop('Email<sup>*</sup>'),
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
			'name' => 'facebook',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Facebook'),
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
			'name' => 'otherContact',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Weitere Kontaktdaten'),
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
			'name' => 'summonerName',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Beschwörer Name<sup>*</sup>'),
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

		if($this->getShowIsSub()){
			$this->add(array(
				'name' => 'isSub',
				'type' => 'Select',
				'options' => array(
					'label' => gettext('Ersatzspieler<sup>*</sup>'),
					'label_options' => array(
						'disable_html_escape' => true,
					),
					'options' => $this->getSubOnly() ? array('2' => $this->isSubOptions['2']) : $this->isSubOptions,
					'column-size' => 'sm-10',
					'label_attributes' => array(
						'class' => 'col-sm-2',
					),
				),
			));
		}

		if($this->getShowAnmerkung()){
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
		}
	}

	public function getInputFilterSpecification() {
		$conditionalRequire = function($value, $context){
			// callback validator
			// returns true if $value is not empty or all context data is empty
			// returns false otherwise
			$values = array_values(array_unique(array_map('trim', $context)));
			$noData = count($values) === 1 && empty($values[0]);
			if($noData){
				return true;
			}
			return !empty($value);
		};
		
		$filters = array();
		
		$textFields = array('name' => true, 'email' => true, 'facebook' => false, 'otherContact' => false, 'summonerName' => true, 'anmerkung' => false);
		foreach($textFields as $field => $required){
			$filters[$field] = array(
				'required' => $required,
				'allow_empty' => !$required || !$this->getDataRequired(),
				'continue_if_empty' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
				'validators' => array(),
			);
			if($required){
				$filters[$field]['validators'][] = array(
					'name' => 'callback',
					'options' => array(
						'callback' => $conditionalRequire,
						'message' => 'Das Feld darf nicht leer sein',
					),
				);
			}
		}
		
		// check that email and summonerName are always unique for the tournament
		// also works for creation since the ID of the context is 0/null
		$filters['email']['validators'][] = array(
			'name' => UniqueObjectInTournament::class,
			'options' => array(
				'entity_class' => Registration::class,
				'fields' => 'email',
				'use_context' => true,
			),
		);
		$filters['summonerName']['validators'][] = array(
			'name' => UniqueObjectInTournament::class,
			'options' => array(
				'entity_class' => Registration::class,
				'fields' => 'summonerName',
				'use_context' => true,
			),
		);
		
		$filters['email']['validators'][] = array(
			'name' => EmailIsNotTim::class,
		);
		if($this->getRequireRWTH()){
			// require RWTH mail
			$filters['email']['validators'][] = array(
				'name' => EmailIsRwth::class,
			);
		}
		
		if($this->getShowTeamName()){
			$filters['teamName'] = array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
			);
		}
		
		$filters['isSub'] = array(
			'required' => false,
			'filters' => array(
				array('name' => 'Int'),
			),
			'validators' => array(
				array('name' => 'Between', 'options' => array('min' => 0, 'max' => 2))
			)
		);
		
		$filters['id'] = array(
			'required' => false,
			'filters' => array(
				array('name' => 'Int'),
			),
		);
		return $filters;
	}
}
