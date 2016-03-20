<?php
namespace GGACTournament\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

use GGACTournament\Form\Element\SubstituteSelect;

/**
 * Match result Form
 */
class SubstituteSelectForm extends Form implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		// we want to ignore the name passed
		parent::__construct('SubstituteSelectForm', $options);
		$this->setAttribute('method', 'post');
	}

	public function init(){
		parent::init();
		$this->setInputFilter(new InputFilter());
		
		$this->add(array(
			'name' => 'substitute',
			'type' => SubstituteSelect::class,
			'options' => array(
				'display_empty_item' => true,
				'empty_item_label' => gettext_noop('-- Substitute --'),
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
			'name' => 'substituteselect_csrf',
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
			'substitute' => array(
				'required' => true,
			),
		);
		return $filters;
	}

}
