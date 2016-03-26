<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;
use GGACTournament\Form\Element\AdminSelect;
use Zend\InputFilter\FileInput;
use Zend\Filter\File\RenameUpload;
use Zend\Validator\File\Extension;
use Zend\Validator\File\IsImage;

use GGACTournament\Entity\Game;

/**
 * Game result Fieldset
 */
class GameResultFieldset extends Fieldset implements InputFilterProviderInterface{
	protected $possibleResults = array('' => '-', '1-0' => '1-0', '0-1' => '0-1','+--' => '1-0 kampflos','--+' => '0-1 kampflos');
	
	protected $isHome;
	
	protected $isRequired;
	
	public function __construct($name = "", $options = array()){
		if(is_array($name) && empty($options)){
			$options = $name;
			$name = "";
		}
		if($name == ""){
			$name = 'GameResultFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function setOptions($options) {
		parent::setOptions($options);
		
		if(isset($options['isHome'])){
			$this->setIsHome($options['isHome']);
		}
		
		if(isset($options['isRequired'])){
			$this->setIsRequired($options['isRequired']);
		}
		
		return $this;
	}
	
	public function getIsRequired() {
		return $this->isRequired;
	}

	public function setIsRequired($isRequired) {
		$this->isRequired = $isRequired;
		return $this;
	}
	
	public function getIsHome() {
		return $this->isHome;
	}

	public function setIsHome($isHome) {
		$this->isHome = $isHome;
		if($this->has('meldung' . (!$isHome ? 'Home' : 'Guest'))){
			$this->remove('meldung' . (!$isHome ? 'Home' : 'Guest'));
		}
		if($this->has('screen' . (!$isHome ? 'Home' : 'Guest'))){
			$this->remove('screen' . (!$isHome ? 'Home' : 'Guest'));
		}
		if($this->has('anmerkung' . (!$isHome ? 'Home' : 'Guest'))){
			$this->remove('anmerkung' . (!$isHome ? 'Home' : 'Guest'));
		}
		return $this;
	}

	public function init() {
		parent::init();
		$this->setObject(new Game());
		
		for($isHome = 0; $isHome <= 1; $isHome ++){
			if($this->getIsHome() !== null && $isHome != $this->isHome){
				continue;
			}
			$this->add(array(
				'name' => 'meldung' . ($isHome ? 'Home' : 'Guest') ,
				'type' => 'Select',
				'options' => array(
					'label' => gettext_noop('Ergebnis'),
					'options' => $this->possibleResults,
					'column-size' => 'sm-8',
					'twb-form-group-size' => 'col-sm-6',
					'label_attributes' => array(
						'class' => 'col-sm-4',
					),
				),
				'attributes' => array(
					'id' => "",
				)
			));

			$this->add(array(
				'name' => 'screen' . ($isHome ? 'Home' : 'Guest'),
				'type' => 'File',
				'options' => array(
					'label' => gettext_noop('Screenshot'),
					'column-size' => 'sm-8',
					'twb-form-group-size' => 'col-sm-6',
					'label_attributes' => array(
						'class' => 'col-sm-4',
					),
				),
				'attributes' => array(
					'id' => "",
				)
			));

			$this->add(array(
				'name' => 'anmerkung' . ($isHome ? 'Home' : 'Guest') ,
				'type' => 'Textarea',
				'options' => array(
	//				'label' => gettext_noop('Anmerkung'),
					'column-size' => 'sm-12',
	//				'label_attributes' => array(
	//					'class' => 'col-sm-2',
	//				),
				),
				'attributes' => array(
					'id' => "",
					"placeholder" => gettext_noop('Anmerkung'),
					"style" => 'width: 94%; margin-left: 3%;',
				)
			));
		}
		
	}
	
	public function getInputFilterSpecification() {
		if(null === $this->getIsHome()){
			return array();
		}
		
		$filters = array(
			'anmerkung' . ($this->getIsHome() ? 'Home' : 'Guest') => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
			),


			'meldung' . ($this->getIsHome() ? 'Home' : 'Guest') => array(
				'required' => $this->getIsRequired(),
			),
			
			'screen' . ($this->getIsHome() ? 'Home' : 'Guest') => array(
				"type" => FileInput::class,
				'required' => false,
				'filters' => array(
					array(
						'name' => RenameUpload::class,
						'options' => array(
							'target' => 'public/uploads/screens',
							'randomize' => true,
							'use_upload_extension' => true,
							'use_upload_name' => true
						),
					),
				),
				'validators' => array(
					array('name' => Extension::class, 'options' => array('extension' => array('gif', 'png', 'jpg', 'jpeg'))),
					array('name' => IsImage::class),
				),
			),

		);
		return $filters;
	}
}
