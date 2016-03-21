<?php
namespace GGACTournament\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\InputFilter\InputFilter;
use XelaxHTMLPurifier\Filter\HTMLPurifier;
use Zend\InputFilter\InputFilterProviderInterface;
use GGACTournament\Entity\Game;

/**
 * Round Form
 */
class RoundForm extends Form implements InputFilterProviderInterface{
	/** @var boolean */
	protected $showType = true;

	/** @var boolean */
	protected $showGameCount = true;

	/** @var boolean */
	protected $showGameCheck = true;

	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('RoundForm', $options);
		$this->setAttribute('method', 'post');
	}
	
	public function setOptions($options) {
		parent::setOptions($options);
        if (isset($options['show_type'])) {
            $this->setShowType($options['show_type']);
        }
		
        if (isset($options['show_game_count'])) {
            $this->setShowGameCount($options['show_game_count']);
        }
		
        if (isset($options['show_game_check'])) {
            $this->setShowGameCheck($options['show_game_check']);
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
	 * @return boolean
	 */
	public function getShowGameCheck() {
		return $this->showGameCheck;
	}

	/**
	 * @param boolean $showType
	 * @return RoundFieldset
	 */
	public function setShowType($showType) {
		$this->showType = $showType;
		return $this;
	}

	/**
	 * @param boolean $showGameCount
	 * @return RoundFieldset
	 */
	public function setShowGameCount($showGameCount) {
		$this->showGameCount = $showGameCount;
		return $this;
	}

	/**
	 * @param boolean $showGameCheck
	 * @return RoundFieldset
	 */
	public function setShowGameCheck($showGameCheck) {
		$this->showGameCheck = $showGameCheck;
		if(!$showGameCheck && $this->has('gameCheck')){
			$this->remove('gameCheck');
		}
		return $this;
	}

	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());

		$this->add(array(
			'name' => 'round',
			'type' => RoundFieldset::class,
			'options' => array(
				'use_as_base_fieldset' => true,
				'show_type' => $this->getShowType(),
				'show_game_count' => $this->getShowGameCount(),
			),
		));
		
		if($this->getShowGameCheck()){
			$this->add(array(
				'name' => 'gameCheck',
				'type' => 'Select',
				'options' => array(
					'label' => gettext_noop('Game check'),
					'options' => array(
						'' => gettext_noop('-- Game Check --'),
						'tournament' => gettext_noop('Tournament'),
						'currentPhase' => gettext_noop('Current phase'),
						'currentGroup' => gettext_noop('Current group'),
						'previousRound' => gettext_noop('Previous round'),
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
		}
		
		$this->add(array(
			'name' => 'mapType',
			'type' => 'Select',
			'options' => array(
				'label' => gettext_noop('Map Type'),
				'options' => array(
					Game::MAP_TYPE_SUMMONERS_RIFT => Game::MAP_TYPE_SUMMONERS_RIFT,
					Game::MAP_TYPE_HOWLING_ABYSS => Game::MAP_TYPE_HOWLING_ABYSS,
					Game::MAP_TYPE_TWISTED_TREELINE => Game::MAP_TYPE_TWISTED_TREELINE,
					Game::MAP_TYPE_CRYSTAL_SCAR => Game::MAP_TYPE_CRYSTAL_SCAR,
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
			'name' => 'pickType',
			'type' => 'Select',
			'options' => array(
				'label' => gettext_noop('Pick Type'),
				'options' => array(
					Game::PICK_TYPE_TOURNAMENT_DRAFT => Game::PICK_TYPE_TOURNAMENT_DRAFT,
					Game::PICK_TYPE_ALL_RANDOM => Game::PICK_TYPE_ALL_RANDOM,
					Game::PICK_TYPE_BLIND_PICK => Game::PICK_TYPE_BLIND_PICK,
					Game::PICK_TYPE_DRAFT_MODE => Game::PICK_TYPE_DRAFT_MODE,
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
			'name' => 'spectatorType',
			'type' => 'Select',
			'options' => array(
				'label' => gettext_noop('Spectator Type'),
				'options' => array(
					Game::SPECTATOR_TYPE_ALL => Game::SPECTATOR_TYPE_ALL,
					Game::SPECTATOR_TYPE_LOBBYONLY => Game::SPECTATOR_TYPE_LOBBYONLY,
					Game::SPECTATOR_TYPE_NONE => Game::SPECTATOR_TYPE_NONE,
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
			'name' => 'round_csrf',
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

	public function getInputFilterSpecification() {
		$filter = array(
			'gameCheck' => array(
				'required' => false,
			)
		);
		return $filter;
	}

}
