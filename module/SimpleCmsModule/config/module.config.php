<?php
namespace SimpleCmsModule;

use XelaxAdmin\Service\DoctrineHydratedFieldsetFactory;

use BjyAuthorize\Provider;
use BjyAuthorize\Guard;

$guardConfig = array(
	['route' => 'zfcadmin/contentblock',      'roles' => ['moderator'] ],
);

$ressources = array(
	'contentblock'
);

$ressourceAllowRules = array(
	[['moderator'], 'contentblock', 'contentblock/list'],
);

return array(
					
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
	
	'form_elements' => array(
		'factories' => array(
			Form\ContentBlockFieldset::class => DoctrineHydratedFieldsetFactory::class,
		),
	),
	
	'service_manager' => array(
		'factories' => array(
			Service\Block::class => Service\BlockFactory::class,
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
			'contentBlock' => View\Helper\ContentBlockHelper::class,
		),
		'factories' => array(
			View\Helper\ContentBlockHelper::class => Service\ContentBlockHelperFactory::class,
		)
	),
	
	// Site navigation
	'navigation' => array(
		// admin navigation
		'admin' => array(
			'contentblock'      => array('label' => gettext_noop('Contents'),      'route' => 'zfcadmin/contentblock',             'resource' => 'contentblock', 'privilege' => 'contentblock/list' ),
		),
	),
	
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
