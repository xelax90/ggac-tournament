<?php
namespace GGACRiotApi;

return array(
    'service_manager' => include 'service.config.php',
	
	'view_helpers' => array(
		'aliases' => array(
			'ggacLoLDDragon' => View\Helper\DDragonHelper::class,
		),
		'factories' => array(
			View\Helper\DDragonHelper::class => Service\DDragonHelperFactory::class,
		)
	),
	
);
