<?php
namespace GGACLoLTournament;

use BjyAuthorize\Provider;
use BjyAuthorize\Guard;
use GGACTournament\Entity\TournamentPhase;


$guardConfig = array(
	['route' => 'kontakt',      'roles' => ['user', 'guest'] ],
	['route' => 'info',         'roles' => ['user', 'guest'] ],
	['route' => 'authenticate', 'roles' => ['user', 'guest'] ],
);

$ressources = array(
);

$ressourceAllowRules = array(
);

return array(
	'controllers' => array(
		'factories' => array(
			Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
		),
	),
	
	'xelax' => array('list_controller' => array('user' => array('page_length' => 0))),

	'router' => array(
		'routes' => array(
			'home' => array(
				'options' => array(
					'defaults' => array(
						'controller' => Controller\IndexController::class,
						'action' => 'index',
					),
				),
			),
			'kontakt' => array(
				'type' => 'literal',
				'options' => array(
					'route' => '/kontakt',
					'defaults' => array(
						'controller' => Controller\IndexController::class,
						'action' => 'kontakt',
					),
				),
			),
			'info' => array(
				'type' => 'literal',
				'options' => array(
					'route' => '/info',
					'defaults' => array(
						'controller' => Controller\IndexController::class,
						'action' => 'info',
					),
				),
			),
			'authenticate' => array(
				'type' => 'literal',
				'options' => array(
					'route' => '/authenticate',
					'defaults' => array(
						'controller' => Controller\IndexController::class,
						'action' => 'authenticate',
					),
				),
			),
		),
	),
	
	'bjyauthorize' => array(
		// resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            Provider\Resource\Config::class => $ressources,
        ),

		
		'rule_providers' => array(
			Provider\Rule\Config::class => array(
                'allow' => $ressourceAllowRules,
            )
		),
		
        'guards' => array(
            Guard\Route::class => $guardConfig
		),
	),
	
    'service_manager' => include 'service.config.php',
	
	// Site navigation
	'navigation' => array(
		'tournament' => array(
			'home'            => array('label' => gettext_noop('Home'),            'route' => 'home',                  'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_CLOSED),
			'info'            => array('label' => gettext_noop('Info'),            'route' => 'info'),
			'anmeldung'       => array('label' => gettext_noop('Anmeldung'),       'route' => 'registration',          'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_CLOSED),
			'teilnehmer'      => array('label' => gettext_noop('Teilnehmer'),      'route' => 'registration/display',  'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_CLOSED),
			'matches'         => array('label' => gettext_noop('Paarungen'),       'route' => 'matches',               'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_STARTED),
			'standings'       => array('label' => gettext_noop('Tabelle'),         'route' => 'standings',             'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_STARTED),
			'teams'           => array('label' => gettext_noop('Teilnehmer'),      'route' => 'teams',                 'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_STARTED),
			'kontakt'         => array('label' => gettext_noop('Kontakt'),         'route' => 'kontakt'),
		),
		'tournament_login' => array(
			'my-matches'      => array('label' => gettext_noop('Ergebnis/Spieltermin melden'),            'route' => 'my-matches',     'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_STARTED),
			'my-team'         => array('label' => gettext_noop('Dein Team/Ersatzspieler ansehen'),        'route' => 'my-team',        'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_STARTED),
			'logout'          => array('label' => gettext_noop('Logout'),                                 'route' => 'zfcuser/logout')
		),
		// default navigation
		'default' => array(
			'admin'           => null,
			'login'           => null,
			'register'        => null,
			'profile'         => null,
			'change-password' => null,
			'logout'          => null,
			'info'            => array('label' => gettext_noop('Info'),            'route' => 'info',                  'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_CLOSED),
			'anmeldung'       => array('label' => gettext_noop('Anmeldung'),       'route' => 'registration',          'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_CLOSED),
			'teilnehmer'      => array('label' => gettext_noop('Teilnehmer'),      'route' => 'registration/display',  'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_CLOSED),
			'matches'         => array('label' => gettext_noop('Paarungen'),       'route' => 'matches',               'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_STARTED),
			'standings'       => array('label' => gettext_noop('Tabelle'),         'route' => 'standings',             'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_STARTED),
			'teams'           => array('label' => gettext_noop('Teilnehmer'),      'route' => 'teams',                 'tournamentState' => TournamentPhase::TOURNAMENT_STATUS_STARTED),
			'kontakt'         => array('label' => gettext_noop('Kontakt'),         'route' => 'kontakt'),
		),
		// admin navigation
		'admin' => array(
		),
	),
	
	// view options
	'view_manager' => array(
		'template_map' => array(
			'layout/layout'           => __DIR__ . '/../view/layout/ggaclol.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),

);
