<?php
namespace SimpleCmsModule;

use XelaxAdmin\Router\ListRoute;

return array(
	'zfcadmin' => array(
		'child_routes' => array(
			'contentblock'        => array( 'type' => ListRoute::class, 'options' => array( 'controller_options_name' => 'contentblock'        ) ),
		),
	),
);