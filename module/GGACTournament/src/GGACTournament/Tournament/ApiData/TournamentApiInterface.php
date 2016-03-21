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

namespace GGACTournament\Tournament\ApiData;

use GGACTournament\Entity\Game;

/**
 *
 * @author schurix
 */
interface TournamentApiInterface {
	
	/**
	 * Generates tournament codes for passed games. The parameters of the first game are taken as generation arguments. Returns true on success
	 * @param array $games array of Game objects
	 * @param boolean $addAllowed
	 * @return boolean
	 */
	public function requestCodes($games, $addAllowed = false);
	
	/**
	 * Updates the spectatorType, pickType, mapType and allowedSummoners for the game. Returns true on success
	 * @param Game $game
	 * @param boolean $addAllowed Set to true if you want to specify allowedSummonerIds for the API
	 * @return boolean
	 */
	public function updateCode(Game $game, $addAllowed = false);
}
