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

use GGACTournament\Entity\Registration;

/**
 * Api data 
 *
 * @author schurix
 */
class Data {
	/** @var Registration */
	protected $registration;
	
	/** @var int */
	protected $registration_id;
	
	/** @var int */
	protected $summonerId;
	
	/** @var int */
	protected $rankedWins = 0;
	
	/** @var int */
	protected $normalWins = 0;
	
	/** @var string */
	protected $tier = "Unranked";
	
	/** @var int */
	protected $level = 0;
	
	/** @var int */
	protected $profileIconId = 0;
	
	/** @var int */
	protected $score;
	
	/**
	 * @return Registration
	 */
	public function getRegistration() {
		return $this->registration;
	}
	
	/**
	 * @return int
	 */
	public function getRegistration_id() {
		return $this->registration_id;
	}
	
	/**
	 * @return int
	 */
	public function getSummonerId() {
		return $this->summonerId;
	}

	/**
	 * @return int
	 */
	public function getRankedWins() {
		return $this->rankedWins;
	}

	/**
	 * @return int
	 */
	public function getNormalWins() {
		return $this->normalWins;
	}

	/**
	 * @return string
	 */
	public function getTier() {
		return $this->tier;
	}

	/**
	 * @return int
	 */
	public function getLevel() {
		return $this->level;
	}

	/**
	 * @return int
	 */
	public function getProfileIconId() {
		return $this->profileIconId;
	}
	
	/**
	 * @param Registration $registration
	 * @return Data
	 */
	public function setRegistration(Registration $registration) {
		$this->registration = $registration;
		$this->registration_id = $registration->getId();
		return $this;
	}
	
	/**
	 * @param int $summonerId
	 * @return Data
	 */
	public function setSummonerId($summonerId) {
		$this->summonerId = $summonerId;
		return $this;
	}

	/**
	 * @param int $rankedWins
	 * @return Data
	 */
	public function setRankedWins($rankedWins) {
		$this->rankedWins = $rankedWins;
		return $this;
	}
	
	/**
	 * @param int $normalWins
	 * @return Data
	 */
	public function setNormalWins($normalWins) {
		$this->normalWins = $normalWins;
		$this->score = null;
		return $this;
	}
	
	/**
	 * @param string $tier
	 * @return Data
	 */
	public function setTier($tier) {
		$this->tier = $tier;
		$this->score = null;
		return $this;
	}
	
	/**
	 * @param int $level
	 * @return Data
	 */
	public function setLevel($level) {
		$this->level = $level;
		$this->score = null;
		return $this;
	}
	
	/**
	 * @param int $profileIconId
	 * @return Data
	 */
	public function setProfileIconId($profileIconId) {
		$this->profileIconId = $profileIconId;
		return $this;
	}
	
	/**
	 * Computes the player strength.
	 * @param boolean $refresh Set to true if you want to force recomputation
	 * @return int
	 */
	public function getScore($refresh = false) {
		if(null == $this->score || $refresh){
			$score = 0;
			switch(strtolower(substr($this->getTier(), 0, 3))){
				case "bro" : $score += 2; break;
				case "sil" : $score += 3; break;
				case "gol" : $score += 4; break;
				case "pla" : $score += 5; break;
				case "dia" : $score += 6; break;
				case "mas" : $score += 7; break;
				case "cha" : $score += 7; break;
				default:
					$score += 1; 
					if($this->level < 30) 
						break;
					if($this->normalWins >= 300)
						$score += 1;
					if($this->normalWins >= 600)
						$score += 1;
					if($this->normalWins >= 1000)
						$score += 1;
					break;
			}
			$this->score = $score;
		}
		return $this->score;
	}

	/**
	 * Magic function for serialization. Do not save team, hochgereiht and runtergereiht
	 * @return array
	 */
	public function __sleep(){
		return array('registration_id', 'summonerId', 'rankedWins', 'normalWins', 'tier', 'level', 'profileIconId');
	}
}
