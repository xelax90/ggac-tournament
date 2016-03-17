<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

use GGACTournament\Form\Element\RoundTypeSelect;
use GGACTournament\Entity\Round;

/**
 * Round Fieldset
 */
class RoundFieldset extends Fieldset implements InputFilterProviderInterface{
	
	/** @var boolean */
	protected $showType = true;

	/** @var boolean */
	protected $showGameCount = true;

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'RoundFieldset';
		}
		parent::__construct($name, $options);
	}

	public function setOptions($options) {
		parent::setOptions($options);
        if (isset($options['show_type'])) {
            $this->setShowType($options['show_type']);
        }
		
        if (isset($options['show_game_count'])) {
            $this->setShowGameCount($options['show_game_count']);
        }
	}
	
	/**
	 * @return boolean
	 */
	public function getShowType() {
		return $this->showType;
	}

	/**
	 * @return boolean
	 */
	public function getShowGameCount() {
		return $this->showGameCount;
	}

	/**
	 * @param boolean $showType
	 * @return RoundFieldset
	 */
	public function setShowType($showType) {
		$this->showType = $showType;
		if(!$showType && $this->has('roundType')){
			$this->remove('roundType');
		}
		return $this;
	}

	/**
	 * @param boolean $showGameCount
	 * @return RoundFieldset
	 */
	public function setShowGameCount($showGameCount) {
		$this->showGameCount = $showGameCount;
		if(!$showGameCount && $this->has('gamesPerMatch')){
			$this->remove('gamesPerMatch');
		}
		return $this;
	}

	public function init(){
		parent::init();
		$this->setObject(new Round());


		$this->add(array(
			'name' => 'number',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('Number'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'min' => '0',
				'step' => '1'
			)
		));
		
		if($this->getShowType()){
			$this->add(array(
				'name' => 'roundType',
				'type' => RoundTypeSelect::class,
				'options' => array(
					'label' => gettext_noop('Type'),
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
			'name' => 'isHidden',
			'type' => 'Checkbox',
			'options' => array(
				'label' => '',
				'use-switch' => true,
				'checked_value' => '1',
				'label_options' => array(
					'position' => \Zend\Form\View\Helper\FormRow::LABEL_PREPEND,
				),
				'column-size' => 'sm-10 col-sm-offset-2',
			),
			'attributes' => array(
				'id' => "",
				'data-label-text' => gettext_noop('Hidden'),
				'data-label-width' => '100',
				'data-off-color' => 'warning',
				'data-on-text' => 'Yes',
				'data-off-text' => 'No',
			)
		));


		$this->add(array(
			'name' => 'startDate',
			'type' => 'Date',
			'options' => array(
				'label' => gettext_noop('Start date'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
				'format' => 'Y-m-d'
			),
			'attributes' => array(
				'id' => "",
				'step' => 1,
				"placeholder" => 'YYYY-MM-DD'
			)
		));

		$this->add(array(
			'name' => 'duration',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('Duration'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'min' => '0',
				'step' => '1'
			)
		));


		$this->add(array(
			'name' => 'timeForDates',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('Time for dates'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'min' => '0',
				'step' => '1'
			)
		));

		if($this->getShowGameCount()){
			$this->add(array(
				'name' => 'gamesPerMatch',
				'type' => 'Number',
				'options' => array(
					'label' => gettext('Games per match'),
					'column-size' => 'sm-10',
					'label_attributes' => array(
						'class' => 'col-sm-2',
					),
				),
				'attributes' => array(
					'id' => "",
					'min' => '0',
					'step' => '1'
				)
			));
		}


		$this->add(array(
			'name' => 'pointsPerGamePoint',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Points per game point'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'step' => '0.1'
			)
		));


		$this->add(array(
			'name' => 'pointsPerMatchWin',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Points per match win'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'step' => '0.1'
			)
		));


		$this->add(array(
			'name' => 'pointsPerMatchDraw',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Points per match draw'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'step' => '0.1'
			)
		));


		$this->add(array(
			'name' => 'pointsPerMatchLoss',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Points per match loss'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'step' => '0.1'
			)
		));


		$this->add(array(
			'name' => 'pointsPerMatchFree',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Points per match free'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'step' => '0.1'
			)
		));


		$this->add(array(
			'name' => 'ignoreColors',
			'type' => 'Checkbox',
			'options' => array(
				'label' => '',
				'use-switch' => true,
				'checked_value' => '1',
				'label_options' => array(
					'position' => \Zend\Form\View\Helper\FormRow::LABEL_PREPEND,
				),
				'column-size' => 'sm-10 col-sm-offset-2',
			),
			'attributes' => array(
				'id' => "",
				'data-label-text' => gettext_noop('Ignore colors'),
				'data-label-width' => '100',
				'data-off-color' => 'warning',
				'data-on-text' => 'Yes',
				'data-off-text' => 'No',
			)
		));

	}

	public function getInputFilterSpecification() {
		$filters = array(

			'number' => array(
				'required' => true,
			),


			'isHidden' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
			),


			'startDate' => array(
				'required' => true,
			),


			'duration' => array(
				'required' => false,
			),


			'timeForDates' => array(
				'required' => true,
			),


			'pointsPerGamePoint' => array(
				'required' => false,
			),


			'pointsPerMatchWin' => array(
				'required' => false,
			),


			'pointsPerMatchDraw' => array(
				'required' => false,
			),


			'pointsPerMatchLoss' => array(
				'required' => false,
			),


			'pointsPerMatchFree' => array(
				'required' => false,
			),


			'ignoreColors' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
			),

		);
		
		if($this->getShowGameCount()){
			$filters['gamesPerMatch'] = array(
				'required' => true,
			);
		}
		
		if($this->getShowType()){
			$filters['roundType'] = array(
				'required' => true,
			);
		}
		
		return $filters;
	}
}
