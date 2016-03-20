<?php
namespace GGACTournament;

use BjyAuthorize\Provider;
use BjyAuthorize\Guard;

$guardConfig = array(
	//frontend
	['route' => 'registration',                'roles' => ['guest', 'user'] ],
	['route' => 'registration/form',           'roles' => ['guest', 'user'] ],
	['route' => 'registration/confirm',        'roles' => ['guest', 'user'] ],
	['route' => 'registration/ready',          'roles' => ['guest', 'user'] ],
	['route' => 'registration/display',        'roles' => ['guest', 'user'] ],
	
	// backend
	['route' => 'zfcadmin/tournament',               'roles' => ['administrator'] ],
	['route' => 'zfcadmin/registration',             'roles' => ['moderator'] ],
	['route' => 'zfcadmin/matches',                  'roles' => ['moderator'] ],
	['route' => 'zfcadmin/matches/block',            'roles' => ['moderator'] ],
	['route' => 'zfcadmin/matches/unblock',          'roles' => ['moderator'] ],
	['route' => 'zfcadmin/matches/setResult',        'roles' => ['moderator'] ],
	['route' => 'zfcadmin/matches/comment',          'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams',                    'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/unblock',            'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/block',              'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/comment',            'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/edit',               'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/create',             'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/warn',               'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/addSub',             'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/deleteWarning',      'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/player',             'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/player/warn',        'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/player/makeSub',     'roles' => ['moderator'] ],
	['route' => 'zfcadmin/teams/player/makeCaptain', 'roles' => ['moderator'] ],
);

$ressources = array(
	'tournament'
);

$ressourceAllowRules = array(
	[['administrator'], 'tournament', 'tournament/list'],
	[['moderator'], 'tournament', 'matches/list'],
	[['moderator'], 'tournament', 'teams/list'],
	[['moderator'], 'tournament', 'registrations/list'],
	
);

return array(
	'controllers' => array(
		'invokables' => array(
		),
		'factories' => array(
			Controller\RegistrationController::class => Controller\Factory\RegistrationControllerFactory::class,
			Controller\AdminTournamentPhaseController::class => Controller\Factory\AdminTournamentPhaseControllerFactory::class,
			Controller\AdminMatchController::class => Controller\Factory\AdminMatchControllerFactory::class,
			Controller\AdminRoundController::class => Controller\Factory\AdminRoundControllerFactory::class,
			Controller\AdminRegistrationController::class => Controller\Factory\AdminRegistrationControllerFactory::class,
			Controller\AdminTeamController::class => Controller\Factory\AdminTeamControllerFactory::class,
		),
	),
	
	'xelax' => include 'xelax.config.php',
	
	'router' => array(
		'routes' => include 'router.config.php',
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
	
	'skelleton_application' => array(
		'roles' => array(
			'guest' => array(),
			'user' => array(
				'moderator' => array(
					'administrator' => array() // Admin role must be leaf and must contain 'admin'
				)
			)
		)
	),
	
    'service_manager' => include 'service.config.php',
	'form_elements' => include 'forms.config.php',
	'validators' => include 'validators.config.php',
					
	// language options
	'translator' => array(
		'translation_file_patterns' => array(
			array(
				'type'     => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.mo',
			),
		),
	),

	// view options
	'view_manager' => array(
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),

	'view_helpers' => array(
		'aliases' => array(
			'tournamentAcl' => View\Helper\TournamentAcl::class,
		),
		'factories' => array(
			View\Helper\TournamentAcl::class => Service\TournamentAclHelperFactory::class,
		)
	),
	
	// Site navigation
	'navigation' => array(
		// default navigation
		'default' => array(
		),
		// admin navigation
		'admin' => array(
			'users' => null,
			'user-profiles' => null,
			'user-dropdown' => array( 'label' => gettext_noop('Users'),          'route' => 'zfcadmin/user',             'resource' => 'administration', 'privilege' => 'user/list', 'pages' => array( 
				'users'         => array('label' => gettext_noop('Users'),           'route' => 'zfcadmin/user',             'resource' => 'administration', 'privilege' => 'user/list' ),
				'user-profiles' => array('label' => gettext_noop('User Profiles'),   'route' => 'zfcadmin/userprofile',      'resource' => 'administration', 'privilege' => 'userprofile'),
			)),
			'tournament'    => array('label' => gettext_noop('Tournament'),      'route' => 'zfcadmin/matches',          'resource' => 'tournament',     'privilege' => 'matches/list', 'pages' => array(
				'registration'       => array('label' => gettext_noop('Registrations'),         'route' => 'zfcadmin/registration',           'resource' => 'tournament', 'privilege' => 'registrations/list' ),
				'tournament'         => array('label' => gettext_noop('Tournaments'),           'route' => 'zfcadmin/tournament',             'resource' => 'tournament', 'privilege' => 'tournament/list' ),
				'matches'            => array('label' => gettext_noop('Matches'),               'route' => 'zfcadmin/matches',                'resource' => 'tournament', 'privilege' => 'matches/list' ),
				'teams'              => array('label' => gettext_noop('Teams'),                 'route' => 'zfcadmin/teams',                  'resource' => 'tournament', 'privilege' => 'teams/list' ),
			)),
		),
	),


	// Placeholder for console routes
	'console' => array(
		'router' => array(
			'routes' => array(
			),
		),
	),

	// doctrine config
	'doctrine' => array(
		'driver' => array(
			__NAMESPACE__ . '_driver' => array(
				'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class, // use AnnotationDriver
				'cache' => 'array',
				'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity') // entity path
			),
			'orm_default' => array(
				'drivers' => array(
					__NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
				)
			)
		),
	),
);

