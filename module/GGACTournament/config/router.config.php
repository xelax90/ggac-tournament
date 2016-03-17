<?php
namespace GGACTournament;

use XelaxAdmin\Router\ListRoute;

return array(
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
	'zfcadmin' => array(
		'child_routes' => array(
			'tournament'        => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'tournament'        ) ),
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
		),
	),
);