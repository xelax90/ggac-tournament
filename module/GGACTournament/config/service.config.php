<?php
namespace GGACTournament;

use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;

return array(
	'abstract_factories' => array(
	),
	'invokables' => array(
	),
	'factories' => array(
		Options\TournamentOptions::class => function (ServiceManager $sm) {
			$config = $sm->get('Config');
			return new Options\TournamentOptions(isset($config['ggac_tournament']) ? $config['ggac_tournament'] : array());
		},
		Tournament\Provider::class => Service\TournamentProviderFactory::class,
		Tournament\Acl::class => Service\TournamentAclFactory::class,
		
		// managers
		Tournament\Manager::class => Service\TournamentManagerFactory::class,
		Tournament\Teamdata\Manager::class => Service\TeamdataManagerFactory::class,
		Tournament\ApiData\Manager::class => Service\ApiDataManagerFactory::class,
		Tournament\Teamdata\TieBreak\Manager::class => Service\TieBreakManagerFactory::class,
		Tournament\RoundCreator\Manager::class => Service\RoundCreatorManagerFactory::class,
		Tournament\TeamMatcher\TeamMatcher::class => Service\TeamMatcherFactory::class,
		Tournament\Registration\Manager::class => Service\RegistrationManagerFactory::class,
		Tournament\Phase\Manager::class => Service\TournamentServiceFactory::class,
				
		// caches
		Tournament\ApiData\Cache::class => Tournament\ApiData\Factory\CacheFactory::class,
		Tournament\Teamdata\Cache::class => Tournament\Teamdata\Factory\CacheFactory::class,
	),
	'aliases' => array(
	)
);