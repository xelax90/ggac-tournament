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

namespace GGACTournament\Tournament\Teamdata\TieBreak;

use GGACTournament\Tournament\Teamdata\Tiebreak\Exception\NoTeamdataManagerException;
use GGACTournament\Tournament\Teamdata\Manager;
use GGACTournament\Tournament\ProviderAwareInterface;

/**
 * All tie break scores must implement this interface
 *
 * @author schurix
 */
interface ScoreInterface extends ProviderAwareInterface{
	
	/**
	 * Sets the teamdata manager
	 * @param Manager $manager
	 */
	public function setTeamdataManager(Manager $manager);
	
	/**
	 * Uses the saved teamdata manager to compute the tiebreak scores and injects them into the teamdata. 
	 * Throws NoTeamdataManagerException if no teamdata manager instance provided.
	 * Tries to avoid recomputation if $refresh is false
	 * @param boolean $refresh
	 * @throws NoTeamdataManagerException
	 */
	public function computeScore($refresh = false);
	
	/**
	 * Reurns the unique key for this tiebreak
	 * @return string
	 */
	public static function getTiebreakKey();
}
