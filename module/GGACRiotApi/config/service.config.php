<?php
namespace GGACRiotApi;

use GGACTournament\Tournament\ApiData\ApiInterface;
use Zend\ServiceManager\ServiceManager;

return array(
	'abstract_factories' => array(
	),
	'invokables' => array(
	),
	'factories' => array(
		Options\ApiOptions::class => function (ServiceManager $sm) {
			$config = $sm->get('Config');
			return new Options\ApiOptions(isset($config['ggac_riotapi']) ? $config['ggac_riotapi'] : array());
		},
		Service\Client::class => Service\ClientFactory::class,
		Cache\ApiCache::class => Service\CacheFactory::class,
	),
	'aliases' => array(
		ApiInterface::class => Service\Client::class,		
	)
);