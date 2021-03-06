<?php
namespace GGACTournament;

use XelaxAdmin\Router\ListRoute;

return array(
	'apidata' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/apidata',
			'defaults' => array(
				'controller' => Controller\ApiDataController::class,
				'action'     => 'index',
			),
		),
		'may_terminate' => false,
		'child_routes' => array(
			'queue' => array(
				'type' => 'literal',
				'options' => array(
					'route'    => '/queue',
					'defaults' => array(
						'action'     => 'queue',
					),
				),
			),
		),
	),
	'registration' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/registration',
			'defaults' => array(
				'controller' => Controller\RegistrationController::class,
				'action'     => 'index',
			),
		),
		'may_terminate' => true,
		'child_routes' => array(
			'form' => array(
				'type' => 'literal',
				'options' => array(
					'route'    => '/form',
					'defaults' => array(
						'action'     => 'form',
					),
				),
			),
			'confirm' => array(
				'type' => 'literal',
				'options' => array(
					'route'    => '/confirm',
					'defaults' => array(
						'action'     => 'confirm',
					),
				),
			),
			'ready' => array(
				'type' => 'literal',
				'options' => array(
					'route'    => '/ready',
					'defaults' => array(
						'action'     => 'ready',
					),
				),
			),
			'display' => array(
				'type' => 'literal',
				'options' => array(
					'route'    => '/display',
					'defaults' => array(
						'action'     => 'display',
					),
				),
			),
		)
	),
	'standings' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/standings',
			'defaults' => array(
				'controller' => Controller\StandingsController::class,
				'action'     => 'table',
			),
		),
	),
	'teams' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/teams',
			'defaults' => array(
				'controller' => Controller\TeamController::class,
				'action'     => 'teams',
			),
		),
	),
	'my-team' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/my-team',
			'defaults' => array(
				'controller' => Controller\TeamController::class,
				'action'     => 'my-team',
			),
		),
	),
	'my-matches' => array(
		'type' => 'Segment',
		'options' => array(
			'route'    => '/my-matches[/:match_id]',
			'defaults' => array(
				'controller' => Controller\UserReportController::class,
				'action'     => 'index',
				'match_id'   => 0,
			),
			'constraints' => array(
				'match_id' => '[0-9]+',
			),
		),
	),
	'matches' => array(
		'type' => 'literal',
		'options' => array(
			'route'    => '/matches',
			'defaults' => array(
				'controller' => Controller\MatchController::class,
				'action'     => 'matches',
			),
		),
	),
	'zfcadmin' => array(
		'child_routes' => array(
			'tournament'        => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'tournament'        ) ),
			'registration'      => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'registration'      ) ),
			'matches' => array(
				'type' => 'Segment',
				'options' => array(
					'route'    => '/matches[/:match_id]',
					'defaults' => array(
						'controller' => Controller\AdminMatchController::class,
						'action'     => 'matchAdmin',
						'match_id'   => 0,
					),
					'constraints' => array(
						'match_id' => '[0-9]+',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'getcodes' => array(
						'type' => 'segment',
						'options' => array(
							'route'    => '/getcodes/:round_id',
							'defaults' => array(
								'action'     => 'roundGetCodes',
							),
							'constraints' => array(
								'round_id' => '[0-9]+',
							),
						),
					),
					'unblock' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/unblock',
							'defaults' => array(
								'action'     => 'matchUnblock',
							),
						),
					),
					'block' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/block',
							'defaults' => array(
								'action'     => 'matchBlock',
							),
						),
					),
					'comment' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/comment',
							'defaults' => array(
								'action'     => 'matchComment',
							),
						),
					),
					'setResult' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/setResult',
							'defaults' => array(
								'action'     => 'matchSetResult',
							),
						),
					),
				),
			),
			'teams' => array(
				'type' => 'Segment',
				'options' => array(
					'route'    => '/:routeStart[/:team_id]',
					'defaults' => array(
						'controller' => Controller\AdminTeamController::class,
						'action'     => 'index',
						'team_id'    => 0,
						'routeStart' => 'teams',
					),
					'constraints' => array(
						'team_id' => '[0-9]+',
						'routeStart' => '(teams|my-teams)'
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'match' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => '/match[/:confirm]',
							'defaults' => array(
								'action'     => 'teamMatcher',
								'confirm'    => ''
							),
							'constraints' => array(
								'confirm' => 'confirm',
							),
						),
					),
					'unblock' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/unblock',
							'defaults' => array(
								'action'     => 'teamUnblock',
							),
						),
					),
					'block' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/block',
							'defaults' => array(
								'action'     => 'teamBlock',
							),
						),
					),
					'comment' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/comment',
							'defaults' => array(
								'action'     => 'teamComment',
							),
						),
					),
					'edit' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/edit',
							'defaults' => array(
								'action'     => 'teamEdit',
							),
						),
					),
					'create' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/create',
							'defaults' => array(
								'action'     => 'teamCreate',
							),
						),
					),
					'warn' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/warn',
							'defaults' => array(
								'action'     => 'teamWarn',
							),
						),
					),
					'addSub' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/add-sub',
							'defaults' => array(
								'action'     => 'teamAddSub',
							),
						),
					),
					'deleteWarning' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => '/deleteWarning/:warning_id',
							'defaults' => array(
								'action'     => 'warningDelete',
							),
							'constraints' => array(
								'warning_id' => '[0-9]+',
							),
						),
					),
					'player' => array(
						'type' => 'Segment',
						'options' => array(
							'route'    => '/player/:player_id',
							'constraints' => array(
								'player_id' => '[0-9]+',
							),
						),
						'may_terminate' => false,
						'child_routes' => array(
							'warn' => array(
								'type' => 'literal',
								'options' => array(
									'route'    => '/warn',
									'defaults' => array(
										'action'     => 'playerWarn',
									),
								),
							),
							'makeSub' => array(
								'type' => 'literal',
								'options' => array(
									'route'    => '/make-sub',
									'defaults' => array(
										'action'     => 'playerMakeSub',
									),
								),
							),
							'makeCaptain' => array(
								'type' => 'literal',
								'options' => array(
									'route'    => '/make-captain',
									'defaults' => array(
										'action'     => 'playerMakeCaptain',
									),
								),
							),
						),
					),
				),
			),
		),
	),
);