<?php
namespace GGACTournament;

use Zend\ServiceManager\Factory\InvokableFactory;
use DoctrineORMModule\Service\ObjectSelectFactory;
use Zend\Form\ElementFactory;
use XelaxAdmin\Service\DoctrineHydratedFieldsetFactory;

return array(
	'factories' => array(
		// elements
		Form\Element\TeamSelect::class => Form\Element\Factory\TournamentObjectSelect::class,
		Form\Element\SubstituteSelect::class => Form\Element\Factory\TournamentObjectSelect::class,
		Form\Element\AdminSelect::class => Form\Element\Factory\GenericObjectSelectFactory::class,
		Form\Element\RegistrationStateSelect::class => ElementFactory::class,
		Form\Element\TournamentStateSelect::class => ElementFactory::class,
		Form\Element\RoundTypeSelect::class => Form\Element\Factory\RoundTypeSelectFactory::class,
		Form\Element\TieBreakSelect::class =>  Form\Element\Factory\TieBreakSelectFactory::class,
		
		// fieldsets
		Form\GroupFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\PlayerFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\RegistrationFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\TeamFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\TournamentFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\TournamentPhaseFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\WarningFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\RoundFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\GameResultFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\MatchUserResultFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\MatchTimeFieldset::class => DoctrineHydratedFieldsetFactory::class,
		Form\GroupTeamMappingFieldset::class => DoctrineHydratedFieldsetFactory::class,

		// forms
		Form\RegistrationTeamForm::class => DoctrineHydratedFieldsetFactory::class,
		Form\RegistrationSingleForm::class => DoctrineHydratedFieldsetFactory::class,
		Form\MatchCommentForm::class => DoctrineHydratedFieldsetFactory::class,
		Form\MatchResultForm::class => DoctrineHydratedFieldsetFactory::class,
		Form\TeamCommentForm::class => DoctrineHydratedFieldsetFactory::class,
		Form\MatchUserResultForm::class => DoctrineHydratedFieldsetFactory::class,
	),
	'aliases' => array(
	)
);