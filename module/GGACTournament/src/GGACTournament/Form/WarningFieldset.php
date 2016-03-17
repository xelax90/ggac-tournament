<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

use GGACTournament\Entity\Warning;

/**
 * Warning Fieldset
 */
class WarningFieldset extends Fieldset implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'WarningFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new Warning());


		$this->add(array(
			'name' => 'comment',
			'type' => 'Textarea',
			'options' => array(
				'label' => gettext_noop('comment'),
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

			'comment' => array(
				'required' => true,
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
