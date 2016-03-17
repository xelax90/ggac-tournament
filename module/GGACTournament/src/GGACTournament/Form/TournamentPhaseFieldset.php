<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

use GGACTournament\Entity\TournamentPhase;
use GGACTournament\Form\Element\RegistrationStateSelect;
use GGACTournament\Form\Element\TournamentStateSelect;
use GGACTournament\Form\Element\RoundTypeSelect;
use GGACTournament\Form\Element\TieBreakSelect;

/**
 * TournamentPhase Fieldset
 */
class TournamentPhaseFieldset extends Fieldset implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'TournamentPhaseFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new TournamentPhase());


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


		$this->add(array(
			'name' => 'name',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Name'),
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
			'name' => 'registrationState',
			'type' => RegistrationStateSelect::class,
			'options' => array(
				'label' => gettext_noop('Registration state'),
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
			'name' => 'tournamentState',
			'type' => TournamentStateSelect::class,
			'options' => array(
				'label' => gettext_noop('Tournament state'),
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
			'name' => 'defaultRoundtype',
			'type' => RoundTypeSelect::class,
			'options' => array(
				'label' => gettext_noop('Default round type'),
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
			'name' => 'resetPoints',
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
				'data-label-text' => gettext_noop('Reset points'),
				'data-label-width' => '100',
				'data-off-color' => 'warning',
				'data-on-text' => 'Yes',
				'data-off-text' => 'No',
			)
		));

		$this->add(array(
			'name' => 'keepGroups',
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
				'data-label-text' => gettext_noop('Keep Groups'),
				'data-label-width' => '100',
				'data-off-color' => 'warning',
				'data-on-text' => 'Yes',
				'data-off-text' => 'No',
			)
		));
		
		$this->add(array(
			'name' => 'tiebreaks',
			'type' => TieBreakSelect::class,
			'options' => array(
				'label' => gettext_noop('Tie breaks'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'data-fancy' => '1',
				'data-placeholder' => gettext_noop('Tie breaks'),
				'data-allow-clear' => '1',
				"multiple" => true,
				'data-reorder' => '1',
			)
		));
		
	}

	public function getInputFilterSpecification() {
		$filters = array(

			'number' => array(
				'required' => false,
			),


			'name' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
			),


			'registrationState' => array(
				'required' => true,
			),


			'tournamentState' => array(
				'required' => true,
			),


			'defaultRoundtype' => array(
				'required' => true,
			),


			'resetPoints' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
			),

			'keepGroups' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
			),
			
			'tiebreaks' => array(
				'required' => false,
			),

		);
		return $filters;
	}
}
