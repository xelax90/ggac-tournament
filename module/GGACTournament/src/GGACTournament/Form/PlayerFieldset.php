<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;
use GGACTournament\Form\Element\TeamSelect;
use GGACTournament\Entity\Player;
use GGACTournament\Entity\Team;

/**
 * Player Fieldset
 */
class PlayerFieldset extends Fieldset implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'PlayerFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new Player());
		
		$this->add(array(
			'name' => 'team',
			'type' => TeamSelect::class,
			'options' => array(
				'display_empty_item' => true,
				'empty_item_label' => gettext_noop('-- Ersatzspieler --'),
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
			'name' => 'registration',
			'type' => RegistrationFieldset::class,
			'options' => array(
				'show_isSub' => false,
				'show_anmerkung' => false,
			),
			'attributes' => array(
			)
		));
		
		$this->add(array(
			'name' => 'isCaptain',
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
				'data-label-text' => gettext_noop('Captain'),
				'data-label-width' => '100',
				'data-off-color' => 'warning',
				'data-on-text' => 'Yes',
				'data-off-text' => 'No',
			)
		));

	}

	public function getInputFilterSpecification() {
		$filters = array(

			'isCaptain' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
			),

		);
		return $filters;
	}
}
