<?php
namespace GGACTournament\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use XelaxHTMLPurifier\Filter\HTMLPurifier;
use GGACTournament\Form\Element\TeamSelect;

use GGACTournament\Entity\GroupTeamMapping;

/**
 * GroupTeamMapping Fieldset
 */
class GroupTeamMappingFieldset extends Fieldset implements InputFilterProviderInterface{
	
	/** @var boolean */
	protected $showTeamSelect = true;
	
	public function __construct($name = "", $options = array()){
		if($name == ""){
			$name = 'GroupTeamMappingFieldset';
		}
		parent::__construct($name, $options);
	}
	
	public function getShowTeamSelect() {
		return $this->showTeamSelect;
	}

	public function setShowTeamSelect($showTeamSelect) {
		$this->showTeamSelect = $showTeamSelect;
		if(!$showTeamSelect && $this->has('team')){
			$this->remove('team');
		}
		return $this;
	}

	public function setOptions($options) {
		if(isset($options['show_team_select'])){
			$this->setShowTeamSelect($options['show_team_select']);
		}
		return parent::setOptions($options);
		
	}

	public function init(){
		parent::init();
		$this->setObject(new GroupTeamMapping());
		
		if($this->getShowTeamSelect()){
			$this->add(array(
				'name' => 'team',
				'type' => TeamSelect::class,
				'options' => array(
					'column-size' => 'sm-10',
					'label_attributes' => array(
						'class' => 'col-sm-2',
					),
				),
				'attributes' => array(
					'id' => "",
					'data-fancy' => "1",
				)
			));
		}

		$this->add(array(
			'name' => 'seed',
			'type' => 'Number',
			'options' => array(
				'label' => gettext('Seed'),
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
			'seed' => array(
				'required' => false,
			),
		);
		if($this->getShowTeamSelect()){
			$filters['team'] = array(
				'required' => true,
			);
		}
		return $filters;
	}
}
