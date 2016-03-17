<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

use GGACTournament\Entity\Group;

/**
 * Group Fieldset
 */
class GroupFieldset extends Fieldset implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'GroupFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new Group());


		$this->add(array(
			'name' => 'number',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('number'),
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

	public function getInputFilterSpecification() {
		$filters = array(

			'number' => array(
				'required' => true,
			),

		);
		return $filters;
	}
}
