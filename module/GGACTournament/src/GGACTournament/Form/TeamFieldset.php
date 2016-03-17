<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;
use GGACTournament\Form\Element\AdminSelect;

use GGACTournament\Entity\Team;

/**
 * Team Fieldset
 */
class TeamFieldset extends Fieldset implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'TeamFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new Team());


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
			'name' => 'ansprechpartner',
			'type' => AdminSelect::class,
			'options' => array(
				'label' => gettext_noop('Ansprechpartner'),
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
			'name' => 'isBlocked',
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
				'data-label-text' => gettext_noop('Blocked'),
				'data-label-width' => '100',
				'data-off-color' => 'warning',
				'data-on-text' => 'Yes',
				'data-off-text' => 'No',
			)
		));


		$this->add(array(
			'name' => 'icon',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Icon'),
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

	public function getInputFilterSpecification() {
		$filters = array(

			'name' => array(
				'required' => true,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
			),


			'number' => array(
				'required' => true,
			),

			'ansprechpartner' => array(
				'required' => true,
			),


			'isBlocked' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
			),


			'icon' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
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

		);
		return $filters;
	}
}
