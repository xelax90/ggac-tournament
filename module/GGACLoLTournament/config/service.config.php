<?php
namespace GGACLoLTournament;

return array(
	'abstract_factories' => array(
	),
	'invokables' => array(
	),
	'factories' => array(
		'tournament_navigation' => Navigation\TournamentNavigationFactory::class,
		'login_navigation' => Navigation\LoginNavigationFactory::class,
	),
	'aliases' => array(
	)
);