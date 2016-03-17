<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;

use GGACTournament\Entity\Tournament;

/**
 * Tournament Fieldset
 */
class TournamentFieldset extends Fieldset implements InputFilterProviderInterface{

	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'TournamentFieldset';
		}
		parent::__construct($name, $options);
	}

	public function init(){
		parent::init();
		$this->setObject(new Tournament());


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
			'name' => 'rulesFile',
			'type' => 'File',
			'options' => array(
				'label' => gettext_noop('Rules file'),
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
			'name' => 'announcementFile',
			'type' => 'File',
			'options' => array(
				'label' => gettext_noop('Announcement file'),
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
			'name' => 'minimumSubs',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('Minimum substitutes'),
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
			'name' => 'registrationTeamSize',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('Team size'),
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
			'name' => 'registrationSingleRequireRWTH',
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
				'data-label-text' => gettext_noop('Single require RWTH'),
				'data-label-width' => '200',
				'data-off-color' => 'warning',
				'data-on-text' => 'Yes',
				'data-off-text' => 'No',
			)
		));


		$this->add(array(
			'name' => 'registrationTeamMinRWTH',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Team minimum RWTH'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'min' => '0',
				'step' => '0.01'
			)
		));


		$this->add(array(
			'name' => 'registrationTeamMaxNotRWTH',
			'type' => 'Number',
			'options' => array(
				'label' => gettext_noop('Team maximum not RWTH'),
				'column-size' => 'sm-10',
				'label_attributes' => array(
					'class' => 'col-sm-2',
				),
			),
			'attributes' => array(
				'id' => "",
				'min' => '0',
				'step' => '0.01'
			)
		));


		$this->add(array(
			'name' => 'registrationTeamMaxMembers',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('team maximum members'),
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

			'name' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
			),

			'rulesFile' => array(
				"type" => "Zend\InputFilter\FileInput",
				'required' => false,
				'filters' => array(
					array(
						'name' => 'Zend\Filter\File\RenameUpload',
						'options' => array(
							'target' => 'public/uploads/rules',
							'randomize' => true,
							'use_upload_extension' => true,
							'use_upload_name' => true
						),
					),
				),
				'validators' => array(
				),
			),

			'announcementFile' => array(
				"type" => "Zend\InputFilter\FileInput",
				'required' => false,
				'filters' => array(
					array(
						'name' => 'Zend\Filter\File\RenameUpload',
						'options' => array(
							'target' => 'public/uploads/announcements',
							'randomize' => true,
							'use_upload_extension' => true,
							'use_upload_name' => true
						),
					),
				),
				'validators' => array(
				),
			),
			
			'minimumSubs' => array(
				'required' => false,
			),


			'registrationTeamSize' => array(
				'required' => false,
			),


			'registrationSingleRequireRWTH' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'Digits'),
				),
			),


			'registrationTeamMinRWTH' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
			),


			'registrationTeamMaxNotRWTH' => array(
				'required' => false,
				'filters' => array(
					array('name' => 'StringTrim'),
					array('name' => 'StripTags'),
					array('name' => HTMLPurifier::class),
				),
			),


			'registrationTeamMaxMembers' => array(
				'required' => false,
			),

		);
		return $filters;
	}
}
