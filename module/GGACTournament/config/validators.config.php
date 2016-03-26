<?php
namespace GGACTournament;

use Zend\ServiceManager\Factory\InvokableFactory;
use DoctrineModule\Validator\ObjectExists;
use DoctrineModule\Validator\NoObjectExists;
use DoctrineModule\Validator\UniqueObject;


return array(
	'factories' => array(
		ObjectExists::class => Validator\Factory\ObjectExistsFactory::class,
		NoObjectExists::class => Validator\Factory\ObjectExistsFactory::class,
		UniqueObject::class => Validator\Factory\UniqueObjectFactory::class,
		Validator\ObjectExistsInTournament::class => Validator\Factory\ObjectExistsInTournamentFactory::class,
		Validator\NoObjectExistsInTournament::class => Validator\Factory\ObjectExistsInTournamentFactory::class,
		Validator\UniqueObjectInTournament::class => Validator\Factory\UniqueObjectInTournamentFactory::class,
		Validator\EmailIsRwth::class => InvokableFactory::class,
		Validator\MinMaxEmailsRwth::class => Validator\Factory\MinMaxEmailsRwthFactory::class,
		Validator\MinMaxEmailsNotRwth::class => Validator\Factory\MinMaxEmailsRwthFactory::class,
		Validator\MatchReport::class => InvokableFactory::class,
	),
	'aliases' => array(
	)
);