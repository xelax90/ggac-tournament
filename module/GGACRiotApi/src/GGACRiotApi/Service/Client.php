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

namespace GGACRiotApi\Service;

use GGACTournament\Tournament\ApiData\ApiInterface;

use GGACRiotApi\Options\ApiOptions;
use GGACRiotApi\Cache\ApiCache;
use GGACTournament\Tournament\ApiData\Data;
use GGACTournament\Tournament\ApiData\Feature\ValidateSummonerName;

/**
 * Description of Client
 *
 * @author schurix
 */
class Client implements ApiInterface, ValidateSummonerName{
	
	/** @var ApiOptions */
	protected $options;
	
	/** @var ApiCache */
	protected $cache;
	
	/** @var int */
	protected static $requestCount = 0;
	
	public function getOptions() {
		return $this->options;
	}

	public function setOptions(ApiOptions $options) {
		$this->options = $options;
		return $this;
	}
	
	public function getCache() {
		return $this->cache;
	}

	public function setCache(ApiCache $cache) {
		$this->cache = $cache;
		return $this;
	}
	
	public static function getStandardName($summonername){
		$res = mb_strtolower($summonername, 'UTF-8');
		$res = str_replace(array(","," ","&"), "", $res);
		$res = str_replace(array("Ä","Ö","Ü", "Ø"), array("ä", "ö", "ü", "ø"), $res);
		return $res;
	}

	public function summonerNameIsValid($summonerName) {
		return $this->getSummoner($summonerName) != 404;
	}
	
	public function getApiData($registrations) {
		$summoners = $this->getSummoners($registrations);
		$result = array();
		
		foreach($registrations as $registration){
			/* @var $registration \GGACTournament\Entity\Registration */
			$data = new Data();
			$summoner = null;
			if(!empty($registration->getSummonerId()) && !empty($summoners['byId'][$registration->getSummonerId()])){
				$summoner = $summoners['byId'][$registration->getSummonerId()];
			} elseif(!empty($registration->getSummonerName()) && !empty($summoners['byName'][$registration->getSummonerName()])){
				$summoner = $summoners['byName'][$registration->getSummonerName()];
			}
			if($summoner){
				$data->setLevel($summoner->summonerLevel);
				$registration->setSummonerId($summoner->id);
				$registration->setSummonerName($summoner->name);
				$data->setProfileIconId($summoner->profileIconId);
				$data->setNormalWins($this->getNormalWins($summoner->id));
				$data->setRankedWins($this->getRankedWins($summoner->id));
				$data->setTier($this->getRecentLeague($summoner->id));
			}
			$result[$registration->getId()] = $data;
		}
		
		return $result;
	}
	
	/**
	 * Returns array of summoners with standardized name as key.
	 * @param $registrations Array of Registration
	 */
	public function getSummoners($registrations){
		// TODO Benchmark!!
		
		$names = array();
		$ids = array();
		foreach($registrations as $registration){
			/* @var $registration \GGACTournament\Entity\Registration */
			if($registration->getSummonerId()){
				$ids[] = $registration->getSummonerId();
			} else {
				$names[] = static::getStandardName($registration->getSummonerName());
			}
		}
		
		// Split names and ids in 40-element chunks
		$nameChunks = array_chunk($names, 40);
		$idChunks = array_chunk($ids, 40);
		
		// request all chunks
		$summonerApiResults = array();
		foreach($nameChunks as $names){
			$summonerApiResults[] = $this->getSummoner(implode(',', $names));
		}
		foreach($idChunks as $ids){
			$summonerApiResults[] = $this->getSummonerById(implode(',', $ids));
		}
		
		$byName = array();
		$byId = array();
		// join chunks into one array
		foreach($summonerApiResults as $summonerResult){ // for each chunk
			if(is_numeric($summonerResult)){ // if error
				continue; // ignore
			}
			$res = get_object_vars($summonerResult); // api gives object
			foreach($res as $val){
				$byName[$val->name] = $val;
				$byId[$val->id] = $val;
			}
		}
		
		return array('byName' => $byName, 'byId' => $byId);
	}
	
	public function getNormalWins($summonerID){
		$wins = 0;
		$stats = $this->getStats($summonerID);
		if(is_numeric($stats) || empty($stats->playerStatSummaries)){
			return $wins;
		}

		foreach($stats->playerStatSummaries as $summary){
			if($summary->playerStatSummaryType == "Unranked"){
				$wins = $summary->wins;
				break;
			}
		}
		return $wins;
	}
	
	public function getRankedWins($summonerID){
		$wins = 0;
		$stats = $this->getStats($summonerID);
		if(is_numeric($stats) || empty($stats->playerStatSummaries)){
			return $wins;
		}

		foreach($stats->playerStatSummaries as $summary){
			if($summary->playerStatSummaryType == "RankedSolo5x5"){
				$wins = $summary->wins;
				break;
			}
		}
		return $wins;
	}
	
	public function getRecentLeague($summonerID){
		$unranked = 'Unranked';
		$current = $this->getCurrentLeague($summonerID);
		if($current != $unranked){
			return $current;
		}
		// get most recent ranked match
		$matchList = $this->getMatchList($summonerID, null, null, array('beginIndex' => 0, 'endIndex' => 1));
		if(is_numeric($matchList)){
			return $unranked;
		}
		if(empty($matchList->matches)){
			return $unranked;
		}
		$match = $this->getMatch($matchList->matches[0]->matchId);
		if(is_numeric($match)){
			return $unranked;
		}
		
		$identities = $match->participantIdentities;
		$participantId = 0;
		foreach($identities as $identity){
			if($identity->player->summonerId == $summonerID){
				$participantId = $identity->participantId;
				break;
			}
		}
		if(!$participantId){
			return $unranked;
		}
		$participants = $match->participants;
		foreach($participants as $participant){
			if($participant->participantId == $participantId){
				return ucfirst(strtolower($participant->highestAchievedSeasonTier));
			}
		}
		return $unranked;
	}
	
	public function getCurrentLeague($summonerID){
		$leagueEntries = $this->getLeagueEntry($summonerID);
		$leagueEntry = 404;
		if(!is_numeric($leagueEntries)){
			$leagueEntries = $leagueEntries->$summonerID;
			foreach($leagueEntries as $entry){
				//var_dump($entry);
				if($entry->queue == "RANKED_SOLO_5x5" && !empty($entry->entries)){
					$leagueEntry = $entry;
				}
			}
		}
		$league = "Unranked";
		if(!is_numeric($leagueEntry)){
			$league = ucfirst(strtolower($leagueEntry->tier))." ".$leagueEntry->entries[0]->division;
		}
		return $league;
	}
	
	public function getRealm(){
		$endpoint = "/api/lol/static-data/".$this->getOptions()->getRegion()."/v1.2/realm";
		$result = $this->request($endpoint);
		return $result;
	}
	
	public function getMatch($matchID, $includeTimeline = false){
		$endpoint = "/api/lol/".$this->getOptions()->getRegion()."/v2.2/match/".$matchID;
		$params = array();
		if($includeTimeline){
			$params['includeTimeline'] = $includeTimeline ? '1' : '0';
		}
		$result = $this->request($endpoint, $params);
		return $result;
	}
	
	/**
	 * @param int $summonerID
	 * @param array $rankedQueues
	 * @return int|stdClass
	 */
	public function getMatchList($summonerID, $rankedQueues = null, $seasons = null, $moreParams = array()){
		$endpoint = "/api/lol/".$this->getOptions()->getRegion()."/v2.2/matchlist/by-summoner/".$summonerID;
		
		$params = $moreParams;
		if($rankedQueues === null){
			$rankedQueues = ['TEAM_BUILDER_DRAFT_RANKED_5x5', 'RANKED_SOLO_5x5'];
		}
		$params['rankedQueues'] = rawurlencode(implode(',', $rankedQueues));
		if($seasons !== null){
			$params['seasons'] = rawurlencode(implode(',', $seasons));
		}
		
		$result = $this->request($endpoint, $params);
		return $result;
	}
	
	public function getSummoner($summonerNames){
		$endpoint = "/api/lol/".$this->getOptions()->getRegion()."/v1.4/summoner/by-name/".rawurlencode($summonerNames);
		$result = $this->request($endpoint);
		return $result;
	}

	public function getSummonerById($summonerIDs){
		$endpoint = "/api/lol/".$this->getOptions()->getRegion()."/v1.4/summoner/".rawurlencode($summonerIDs);
		$result = $this->request($endpoint);
		return $result;
	}

	public function getStats($summonerID, $season = "SEASON2015"){
		$endpoint = "/api/lol/".$this->getOptions()->getRegion()."/v1.3/stats/by-summoner/".$summonerID."/summary";
		$params = array(
			'season' => $season,
		);
		$result = $this->request($endpoint, $params);
		return $result;
	}
	
	public function getLeagueEntry($summonerID){
		$endpoint="/api/lol/".$this->getOptions()->getRegion()."/v2.5/league/by-summoner/".$summonerID."/entry";
		$result = $this->request($endpoint);
		return $result;
	}
	
	protected function getBaseUrl($endpoint){
		$options = $this->getOptions();
		$url = $options->getProtocol() . '://'.$options->getRegion().'.'.$options->getUrl();
		$url .= '/'.$endpoint;
		return $url;
	}
	
	protected function getCacheKey($url){
		return hash('sha256', $url);
	}

	protected function request($endpoint, $parameters = array()){
		$options = $this->getOptions();
		$baseUrl = $this->getBaseUrl($endpoint);
		$parameters['api_key'] = $options->getKey();
		
		$request = $baseUrl.'?'.http_build_query($parameters);
		
		$cache = $this->getCache();
		
		$cacheKey = $this->getCacheKey($request);
		$contents = null;
		if($cache->hasItem($cacheKey)){
			//echo 'cached';
			$contents = $cache->getItem($cacheKey);
		}
		
		// new request if no cache or if expired cache and stil requests left
		if(($contents != null && $cache->itemHasExpired($cacheKey) && static::$requestCount <= $options->getMaxRequests()) || $contents == null) {
			@$requestContent = file_get_contents($request);
		    // Retrieve HTTP status code
		    list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);

		    // Check the HTTP Status code
		    switch($status_code) {
		        case 200:
		                $error_status="200: Success";
		                break;
		        case 401:
		                $error_status="401: Login failure.  Try logging out and back in.  Password are ONLY used when posting.";
		                break;
		        case 400:
		                $error_status="400: Invalid request.  You may have exceeded your rate limit.";
		                break;
		        case 404:
		                $error_status="404: Not found.  This shouldn't happen.  Please let me know what happened using the feedback link above.";
		                break;
		        case 429:
		                $error_status="429: Rate Limit Exceeded.";
		                break;
		        case 500:
		                $error_status="500: Twitter servers replied with an error. Hopefully they'll be OK soon!";
		                break;
		        case 502:
		                $error_status="502: Twitter servers may be down or being upgraded. Hopefully they'll be OK soon!";
		                break;
		        case 503:
		                $error_status="503: Twitter service unavailable. Hopefully they'll be OK soon!";
		                break;
		        default:
		                $error_status="Undocumented error: " . $status_code;
		                break;
		    }
			if($status_code == 200){
				//echo 'not cached';
				$cache->addItem($cacheKey, $requestContent);
				$contents = $requestContent;
				self::$requestCount++;
			} elseif($status_code == 429 && !empty($cache)){ // rate limit exceeded
				// use cache
			} elseif($status_code == 404){ // 404 is sometimes a valid response
				$cache->addItem($cacheKey, '404');
				return 404;
			} else {
				if($contents != null){
					return json_decode($contents);
				}
				return $status_code;
			}
		}
		
		$obj = json_decode($contents);
		return $obj;
	}
}
