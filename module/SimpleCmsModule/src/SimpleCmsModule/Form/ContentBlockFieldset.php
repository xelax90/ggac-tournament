<?php
namespace SimpleCmsModule\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

use SimpleCmsModule\Entity\ContentBlock;

/**
 * ContentBlock Fieldset
 */
class ContentBlockFieldset extends Fieldset implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'ContentBlockFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new ContentBlock());


		$this->add(array(
			'name' => 'position',
			'type' => 'Text',
			'options' => array(
				'label' => gettext_noop('Position'),
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
			'name' => 'ordering',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('Ordering'),
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
			'name' => 'title',
			'type' => 'Textarea',
			'options' => array(
				'label' => gettext_noop('Title'),
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
			'name' => 'content',
			'type' => 'Textarea',
			'options' => array(
				'label' => gettext_noop('Content'),
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

			'position' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
			),


			'ordering' => array(
				'required' => false,
			),


			'title' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => HTMLPurifier::class),
				),
			),


			'content' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => HTMLPurifier::class),
				),
			),

		);
		return $filters;
	}
}
