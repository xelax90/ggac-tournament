<?php
namespace GGACRiotApi;

use BjyAuthorize\Provider;
use BjyAuthorize\Guard;

$guardConfig = array(
	['route' => 'riot/gameresult',             'roles' => ['guest', 'user'] ],
);

$ressources = array(
);

$ressourceAllowRules = array(
);

return array(
	'controllers' => array(
		'invokables' => array(
		),
		'factories' => array(
			Controller\GameResultController::class => Controller\Factory\GameResultControllerFactory::class,
		),
	),
	
    'service_manager' => include 'service.config.php',
	
	'router' => array(
		'routes' => array(
			'riot' => array(
				'type' => 'literal',
				'options' => array(
					'route'    => '/riot',
					'defaults' => array(
						'controller' => Controller\GameResultController::class,
					),
				),
				'may_terminate' => false,
				'child_routes' => array(
					'gameresult' => array(
						'type' => 'literal',
						'options' => array(
							'route'    => '/gameresult',
						),
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
	
	'view_helpers' => array(
		'aliases' => array(
			'ggacLoLDDragon' => View\Helper\DDragonHelper::class,
		),
		'factories' => array(
			View\Helper\DDragonHelper::class => Service\DDragonHelperFactory::class,
		)
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
