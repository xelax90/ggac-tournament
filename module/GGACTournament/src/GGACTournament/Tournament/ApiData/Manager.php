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

use GGACTournament\Tournament\AbstractManager;
use GGACTournament\Entity\Round;
use GGACTournament\Entity\Match;

/**
 * Manages Api data (division, level, etc.)
 *
 * @author schurix
 */
class Manager extends AbstractManager{
	/** @var Cache */
	protected $cache;
	
	/** @var ApiInterface */
	protected $api;
	
	/** @var TournamentApiInterface */
	protected $tournamentApi;
	
	/**
	 * @return ApiInterface
	 */
	public function getApi() {
		return $this->api;
	}
	
	/**
	 * @param ApiInterface $api
	 * @return Manager
	 */
	public function setApi(ApiInterface $api) {
		$this->api = $api;
		return $this;
	}
	
	/**
	 * @return Cache
	 */
	public function getCache() {
		return $this->cache;
	}
	
	/**
	 * @param Cache $cache
	 * @return Manager
	 */
	public function setCache(Cache $cache) {
		$this->cache = $cache;
		return $this;
	}
	
	/**
	 * @return TournamentApiInterface
	 */
	public function getTournamentApi() {
		return $this->tournamentApi;
	}
	
	/**
	 * @param TournamentApiInterface $tournamentApi
	 * @return Manager
	 */
	public function setTournamentApi(TournamentApiInterface $tournamentApi) {
		$this->tournamentApi = $tournamentApi;
		return $this;
	}
	
	/**
	 * Computes hash over all summoner names
	 * @return string
	 */
	protected function getCacheKey(){
		$registrations = $this->getTournamentProvider()->getTournament()->getRegistrations();
		$names = "";
		foreach($registrations as $registration){
			/* @var $registration \GGACTournament\Entity\Registration */
			$names .= '_'.$registration->getSummonerName();
		}
		return hash('crc32b', $names);
	}
	
	/**
	 * Injects api data into registrations
	 * @param boolean $refresh
	 */
	public function setData($refresh = false){
		$apiData = null;
		if(!$refresh){
			// try to get data from cache
			$apiData = $this->getApiDataFromCache();
		}
		
		$registrations = $this->getTournamentProvider()->getTournament()->getRegistrations();
		
		if(empty($apiData)){ // if refresh forced or no data cached
			// request api
			$apiData = $this->getApi()->getApiData($registrations);
			// flush entities to reflect API changes
			$this->getObjectManager()->flush();
			// store data in cache
			$this->getCache()->addItem($this->getCacheKey(), serialize($apiData));
		}
		// update registrations
		foreach($registrations as $registration){
			/* @var $registration \GGACTournament\Entity\Registration */
			$apiData[$registration->getId()]->setRegistration($registration);
			$registration->setData($apiData[$registration->getId()]);
		}
	}
	
	public function requestCodesForRound(Round $round){
		$res = true;
		foreach($round->getMatches() as $match){
			$currentRes = $this->requestCodesForMatch($match);
			if($currentRes !== true){
				$res = false;
			}
		}
		return $res;
	}
	
	public function requestCodesForMatch(Match $match){
		$tournamentApi = $this->getTournamentApi();
		$res = true;
		foreach($match->getGames() as $game){
			$currentRes = $tournamentApi->updateCode($game);
			if($currentRes !== true){
				$res = false;
			}
		}
		return $res;
	}
	
	/**
	 * Runs workQueue on apis if they implement HasWorkingQueueInterface
	 */
	public function workQueue(){
		$api = $this->getApi();
		if($api instanceof HasWorkingQueueInterface){
			$api->workQueue();
		}
		
		$tournamentApi = $this->getTournamentApi();
		if($tournamentApi instanceof HasWorkingQueueInterface){
			$tournamentApi->workQueue();
		}
	}
	
	/**
	 * If the current api can validate summonerNames, the summonerName 
	 * is validated. Otherwise true is always returned.
	 * @param string $summonerName
	 * @return boolean
	 */
	public function validateSummonerName($summonerName){
		$api = $this->getApi();
		if($api instanceof Feature\ValidateSummonerName){
			return $api->summonerNameIsValid($summonerName);
		}
		return true;
	}
	
	/**
	 * Tries to fetch api data from cache
	 * @return array|null
	 */
	protected function getApiDataFromCache(){
		$cacheKey = $this->getCacheKey();
		$cache = $this->getCache();
		if(!$cache->hasItem($cacheKey) || $cache->itemHasExpired($cacheKey)){
			// if no cache entry found or the entry expired
			return null;
		}
		$apiData = unserialize($cache->getItem($cacheKey));
		return $apiData;
	}	
}
