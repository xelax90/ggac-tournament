<?php
namespace GGACTournament\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

/**
 * Match result Form
 */
class MatchResultForm extends Form implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('MatchResultForm', $options);
		$this->setAttribute('method', 'post');
	}

	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'pointsHome',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Points Home'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'min' => '0',
				'step' => '0.1'
			)
		));
		
		$this->add(array(
			'name' => 'pointsGuest',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Points Guest'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'min' => '0',
				'step' => '0.1'
			)
		));
		
		$this->add(array(
			'name' => 'anmerkung',
			'type' => 'Textarea',
			'options' => array(
				'label' => gettext_noop('Comment'),
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
			'name' => 'matchresult_csrf',
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
		$filters = array(
			'pointsHome' => array(
				'required' => false,
			),
			
			'pointsGuest' => array(
				'required' => false,
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
