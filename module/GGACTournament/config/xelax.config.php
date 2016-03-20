<?php

/* 
 * Copyright (C) 2016 schurix
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace GGACTournament;

use XelaxAdmin\Controller\ListController;

return array(
	'list_controller' => array(
		'player' => array(
			'name' => 'Player',
			'controller_class' => Controller\PlayerController::class, 
			'base_namespace' => __NAMESPACE__,
			'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Name') => 'name', gettext_noop('Summoner Name') => 'summonerName', gettext_noop('EMail') => 'email'),
			'route_base' => 'zfcadmin/teams/player', 
			'rest_enabled' => false,
		),
		'myPlayer' => array(
			'name' => 'Player',
			'controller_class' => Controller\PlayerController::class, 
			'base_namespace' => __NAMESPACE__,
			'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Name') => 'name', gettext_noop('Summoner Name') => 'summonerName', gettext_noop('EMail') => 'email'),
			'route_base' => 'zfcadmin/myteams/player', 
			'rest_enabled' => false,
		),
		'tournament' => array(
			'name' => gettext_noop('Tournament'),
			'controller_class' => ListController::class, 
			'base_namespace' => __NAMESPACE__,
			'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Name') => 'name'),
			'list_title' => gettext_noop('Tournaments'),
			'route_base' => 'zfcadmin/tournament',
			'rest_enabled' => false,
			'delete_route' => array(
				'disabled' => true,
			),
			'child_options' => array(
				'phase' => array(
					'name' => gettext_noop('TournamentPhase'),
					'controller_class' => Controller\AdminTournamentPhaseController::class, 
					'base_namespace' => __NAMESPACE__,
					'list_columns' => array(gettext_noop('Number') => 'number', gettext_noop('Name') => 'name'),
					'list_title' => gettext_noop('Tournament Phases'),
					'rest_enabled' => false,
					'delete_route' => array(
						'disabled' => true,
					),
					'child_options' => array(
						'group' => array(
							'name' => gettext_noop('Group'),
							'parentAttributeName' => 'phase',
							'controller_class' => ListController::class, 
							'base_namespace' => __NAMESPACE__,
							'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Number') => 'number'),
							'list_title' => gettext_noop('Groups'),
							'rest_enabled' => false,
							'delete_route' => array(
								'disabled' => true,
							),
							'child_options' => array(
								'round' => array(
									'name' => gettext_noop('Round'),
									'controller_class' => Controller\AdminRoundController::class, 
									'base_namespace' => __NAMESPACE__,
									'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Number') => 'number', gettext_noop('Type') => 'roundType', gettext_noop('Hidden') => 'isHidden', gettext_noop('Start') => 'startDate',  gettext_noop('Config') => 'configString', ),
									'list_title' => gettext_noop('Rounds'),
									'rest_enabled' => false,
								),
							),
						),
					),
				),
			),
		),
		'registration' => array(
			'name' => 'Registration',
			'controller_class' => Controller\AdminRegistrationController::class, 
			'base_namespace' => __NAMESPACE__,
			'list_columns' => array(gettext_noop('Id') => 'id', gettext_noop('Team Name') => 'teamName', gettext_noop('Name') => 'name', gettext_noop('Summoner Name') => 'summonerName', gettext_noop('EMail') => 'email'),
			'route_base' => 'zfcadmin/registration', 
			'page_length' => 0,
			'rest_enabled' => false,
		),
	),
);