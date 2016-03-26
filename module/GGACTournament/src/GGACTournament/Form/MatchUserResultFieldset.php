<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;
use Zend\Form\Element\Collection;
use GGACTournament\Validator\MatchReport;

use GGACTournament\Entity\Match;

/**
 * Match User result Fieldset
 */
class MatchUserResultFieldset extends Fieldset implements InputFilterProviderInterface{

	protected $isHome;
	
	public function __construct($name = "", $options = array()){
		if(is_array($name) && empty($options)){
			$options = $name;
			$name = "";
		}
		if($name == ""){
			$name = 'MatchUserResultFieldset';
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
		if($this->has('games')){
			/* @var $games \Zend\Form\Element\Collection */
			$games = $this->get('games');
			$games->setOptions(array(
                'target_element' => array(
					'type' => GameResultFieldset::class,
					'options' => array(
						'isHome' => $isHome,
					),
				),
			));
		}
		return $this;
	}

	public function init(){
		parent::init();
		$this->setObject(new Match());
		
		
		$this->add(array(
			'name' => 'allStuff',
			'type' => 'Hidden',
			'attributes' => array(
				'value' => 1,
			)
		));
		
		$this->add(array(
			'name' => 'games',
			'type' => Collection::class,
			'options' => array(
				'count' => 3,
                'allow_add' => false,
                'target_element' => array(
					'type' => GameResultFieldset::class,
					'options' => array(
						'isHome' => $this->getIsHome(),
					),
				),
			),
		), array(
			'priority' => 1000,
		));

	}

	public function getInputFilterSpecification() {
		$filters = array(
			'allStuff' => array(
				'required' => true,
				'validators' => array(
					array(
						'name' => MatchReport::class, 
						'options' => array('attribute_key' => 'meldung'. ($this->getIsHome() ? 'Home' : 'Guest'))
					),
				)
			)
		);
		return $filters;
	}
}
