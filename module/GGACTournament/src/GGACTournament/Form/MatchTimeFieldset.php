<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;
use Zend\Form\Element\Collection;

use GGACTournament\Entity\Match;

/**
 * Match User result Fieldset
 */
class MatchTimeFieldset extends Fieldset implements InputFilterProviderInterface{

	protected $isHome;
	
	public function __construct($name = "", $options = array()){
		if(is_array($name) && empty($options)){
			$options = $name;
			$name = "";
		}
		if($name == ""){
			$name = 'MatchTimeFieldset';
		}
		parent::__construct($name, $options);
	}

	public function setOptions($options) {
		parent::setOptions($options);
		
		if(isset($options['isHome'])){
			$this->setIsHome($options['isHome']);
		}
	}
	
	public function getIsHome() {
		return $this->isHome;
	}

	public function setIsHome($isHome) {
		$this->isHome = $isHome;
		if($this->has('time'.($isHome ? 'Guest' : 'Home'))){
			$this->remove('time'.($isHome ? 'Guest' : 'Home'));
		}
		return $this;
	}

	public function init(){
		parent::init();
		$this->setObject(new Match());

		for($isHome = 0; $isHome <= 1; $isHome++){
			if($this->getIsHome() !== null && $this->getIsHome() != $isHome){
				continue;
			}
			$this->add(array(
				'name' => 'time'.($isHome ? 'Home' : 'Guest'),
				'type' => 'DateTimeLocal',
				'options' => array(
					'label' => gettext_noop('Zeit'),
					'column-size' => 'sm-10',
					'label_attributes' => array(
						'class' => 'col-sm-2',
					),
					'format' => 'Y-m-d\TH:i'
				),
				'attributes' => array(
					'id' => "",
					'step' => 1,
					"placeholder" => 'YYYY-MM-DD\THH:MM'
				)
			));
			
		}

	}

	public function getInputFilterSpecification() {
		if($this->getIsHome() === null){
			return array();
		}
		
		$filters = array(
			'time'.($this->getIsHome() ? 'Home' : 'Guest') => array(
				'required' => true,
			),
		);
		return $filters;
	}
}
