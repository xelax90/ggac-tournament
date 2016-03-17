<?php
return array(
    'bower' => array(
        'bower_folder' => array(
            'os' => 'bower_components',
        ),
        'pack_folder' => array(
            'os' => 'public/js',
            'web' => '/js',
        ),
        'debug_folder' => array(
            'os' => 'public/js/dev',
            'web' => '/js/dev',
        ),
        'debug_mode' => true,
		'use_package_json' => true,
		'packs' => array(
			'backend' => array(
				'token' => 'admin',
				'modules' => array(
					'jquery',
					'datatables',
					'bootstrap',
					'bootstrap-switch',
					'select2',
					'jquery-sortable-lists',
				)
			),
			'frontend' => array(
				'token' => 'main',
				'modules' => array(
					'jquery',
					'bootstrap',
					'select2',
				)
			),
			'ggac' => array(
				'token' => 'ggac',
				'modules' => array(
					'jquery',
					'iscroll',
					'bootstrap',
					'select2',
				)
			),
			'ieLT9' => array(
				'token' => 'ieLT9',
				'modules' => array(
					'html5shiv',
					'respond',
				),
				'attributes' =>  array(
					'conditional' => 'lt IE 9',
				),
			)
		)
    ),
);
