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

use GGACRiotApi\Options\ApiOptions;
use Zend\Mvc\Router\RouteStackInterface;
use GGACRiotApi\Entity\RiotTournamentProvider;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Persistence\ProvidesObjectManager;
use GGACTournament\Entity\Tournament;
use GGACTournament\Entity\Game;
use GGACTournament\Tournament\ApiData\TournamentApiInterface;
use GGACRiotApi\Entity\RiotApiRequest;
use GGACTournament\Tournament\ApiData\HasWorkingQueueInterface;

/**
 * RIOT Tournaments API client
 *
 * @author schurix
 */
class TournamentClient implements ObjectManagerAwareInterface, TournamentApiInterface, HasWorkingQueueInterface {

	use ProvidesObjectManager;

	const REQUEST_NAME_UPDATE_CODE = 'updateCode';
	const REQUEST_NAME_REQUEST_CODE = 'requestCode';
	const REQUEST_NAME_PROVIDER = 'provider';
	const REQUEST_NAME_TOURNAMENT = 'tournament';
	const STATUS_CODE_RATE_LIMIT = 429;

	/** @var ApiOptions */
	protected $options;

	/** @var RouteStackInterface */
	protected $router;

	/** @var int */
	protected static $requestCount = 0;

	/** @var RiotTournamentProvider */
	protected $provider;

	/** @var boolean */
	protected $rateLimitExceeded = false;

	public function getProvider() {
		if (null === $this->provider) {
			$em = $this->getObjectManager();
			$provider = $em->getRepository(RiotTournamentProvider::class)->findOneBy(array());
			if (!$provider) {
				$provider = $this->requestProvider();
			}
			$this->provider = $provider;
		}
		return $this->provider;
	}

	protected function requestProvider() {
		$provider = $this->getObjectManager()->getRepository(RiotTournamentProvider::class)->findOneBy(array());
		if ($provider) {
			return $provider;
		}
		$options = $this->getOptions();
		$region = strtoupper($options->getRegion());
		$params = array();
		$routeOptions = array('name' => 'riot/gameresult'); //, 'force_canonical' => true);
		$url = 'http://lol.gaming.rwth-aachen.de' . $this->getRouter()->assemble($params, $routeOptions); // TODO
		$result = $this->request('/tournament/public/v1/provider', 'post', array('region' => $region, 'url' => $url));
		if ($result['statuscode'] === 200) {
			$provider = new RiotTournamentProvider();
			$provider->setId((int) $result['content']);
			$em = $this->getObjectManager();
			$em->persist($provider);
			$em->flush();
			return $provider;
		}
		if ($result['statuscode'] === self::STATUS_CODE_RATE_LIMIT) {
			$this->queueRequest(array(), self::REQUEST_NAME_PROVIDER);
		}
		return $result['statuscode'];
	}

	public function requestTournament(Tournament $tournament) {
		if ($tournament->getApiId()) {
			return true;
		}
		$provider = $this->getProvider();
		if (!$provider || is_numeric($provider)) {
			if ($provider === self::STATUS_CODE_RATE_LIMIT) {
				$this->queueRequest($tournament, self::REQUEST_NAME_TOURNAMENT);
				return self::STATUS_CODE_RATE_LIMIT;
			}
			return false;
		}

		$result = $this->request('/tournament/public/v1/tournament', 'post', array('name' => $tournament->getName(), 'providerId' => $provider->getId()));
		if ($result['statuscode'] === 200) {
			$tournament->setApiId((int) $result['content']);
			$this->getObjectManager()->flush();
			return true;
		}
		if ($result['statuscode'] === self::STATUS_CODE_RATE_LIMIT) {
			$this->queueRequest($tournament, self::REQUEST_NAME_TOURNAMENT);
			return self::STATUS_CODE_RATE_LIMIT;
		}
		return false;
	}

	protected function getAllowedSummonerIds(Game $game) {
		$tournament = $game->getMatch()->getRound()->getGroup()->getPhase()->getTournament();
		$summonerIds = array();
		foreach ($game->getTeamBlue()->getPlayers() as $player) { /* @var $player \GGACTournament\Entity\Player */
			$summonerIds[] = $player->getRegistration()->getSummonerId();
		}
		foreach ($game->getTeamPurple()->getPlayers() as $player) { /* @var $player \GGACTournament\Entity\Player */
			$summonerIds[] = $player->getRegistration()->getSummonerId();
		}
		foreach ($tournament->getSubs() as $player) { /* @var $player \GGACTournament\Entity\Player */
			$summonerIds[] = $player->getRegistration()->getSummonerId();
		}
		return $summonerIds;
	}

	/**
	 * Generates tournament codes for passed games. The parameters of the first game are taken as generation arguments.
	 * @param array $games
	 * @param boolean $addAllowed
	 * @return boolean
	 */
	public function requestCodes($games, $addAllowed = false) {
		/* @var $game Game */
		$game = $games[0];
		$tournament = $game->getMatch()->getRound()->getGroup()->getPhase()->getTournament();
		$tournamentRequest = $this->requestTournament($tournament);
		if (!$tournamentRequest || is_numeric($tournamentRequest)) {
			if ($tournamentRequest === self::STATUS_CODE_RATE_LIMIT) {
				$this->queueRequest(array('games' => $games, 'addAllowed' => $addAllowed), self::REQUEST_NAME_REQUEST_CODE);
				return self::STATUS_CODE_RATE_LIMIT;
			}
			return false;
		}
		$params = array(
			'teamSize' => $tournament->getRegistrationTeamSize(),
			'spectatorType' => $game->getSpectatorType(),
			'pickType' => $game->getPickType(),
			'mapType' => $game->getMapType(),
			'metadata' => json_encode(array('game_id' => $game->getId())),
		);
		if ($addAllowed) {
			$params['allowedSummonerIds'] = array(
				'participants' => $this->getAllowedSummonerIds($game),
			);
		}
		$GETParams = array(
			'tournamentId' => $tournament->getApiId(),
			'count' => count($games),
		);
		$request = $this->request('/tournament/public/v1/code', 'post', $params, $GETParams);
		if ($request['statuscode'] === 200) {
			$codes = json_decode($request['content']);
			$i = 0;
			foreach ($games as $g) { /* @var $g Game */
				$g->setTournamentCode($codes[$i]);
				$this->getObjectManager()->flush($g);
				$i++;
			}
			return true;
		}
		if ($request['statuscode'] === self::STATUS_CODE_RATE_LIMIT) {
			$this->queueRequest(array('games' => $games, 'addAllowed' => $addAllowed), self::REQUEST_NAME_REQUEST_CODE);
		}
		return $request['statuscode'];
	}

	/**
	 * Updates the spectatorType, pickType, mapType and allowedSummoners for the game
	 * @param Game $game
	 * @param boolean $addAllowed Set to true if you want to specify allowedSummonerIds for the API
	 * @return boolean
	 */
	public function updateCode(Game $game, $addAllowed = false) {
		if (!$game->getTournamentCode()) {
			return $this->requestCodes(array($game), $addAllowed);
		}
		$tournament = $game->getMatch()->getRound()->getGroup()->getPhase()->getTournament();
		$tournamentRequest = $this->requestTournament($tournament);
		if (!$tournamentRequest || is_numeric($tournamentRequest)) {
			if ($tournamentRequest === self::STATUS_CODE_RATE_LIMIT) {
				echo 'q1';
				$this->queueRequest(array('game' => $game, 'addAllowed' => $addAllowed), self::REQUEST_NAME_UPDATE_CODE);
				return self::STATUS_CODE_RATE_LIMIT;
			}
			return false;
		}
		$params = array(
			'spectatorType' => $game->getSpectatorType(),
			'pickType' => $game->getPickType(),
			'mapType' => $game->getMapType(),
		);
		if ($addAllowed) {
			$params['allowedSummonerIds'] = array(
				'participants' => $this->getAllowedSummonerIds($game),
			);
		}
		$request = $this->request('/tournament/public/v1/code/' . $game->getTournamentCode(), 'put', $params);
		if ($request['statuscode'] === 200) {
			return true;
		}
		if ($request['statuscode'] === self::STATUS_CODE_RATE_LIMIT) {
			echo 'q2';
			$this->queueRequest(array('game' => $game, 'addAllowed' => $addAllowed), self::REQUEST_NAME_UPDATE_CODE);
		}
		return $request['statuscode'];
	}

	public function getOptions() {
		return $this->options;
	}

	public function getRouter() {
		return $this->router;
	}

	public function setOptions(ApiOptions $options) {
		$this->options = $options;
		return $this;
	}

	public function setRouter(RouteStackInterface $router) {
		$this->router = $router;
		return $this;
	}

	/**
	 * Normalizing the name of the interface, in case there is a / at the start
	 * @param string $interface
	 * @return string
	 */
	protected function normalizeInterface($interface) {
		return '/' . trim($interface, '/');
	}

	/**
	 * Returns url for passed endpoint
	 * @param string $endpoint
	 * @return string
	 */
	protected function getBaseUrl($endpoint) {
		$options = $this->getOptions();
		$url = $options->getProtocol() . '://' . $options->getTournamentUrl();
		$url .= $this->normalizeInterface($endpoint);
		return $url;
	}

	protected function queueRequest($params, $name) {
		$request = new RiotApiRequest();
		$request->setRequestType(RiotApiRequest::REQUEST_TYPE_TOURNAMENT);
		$data = array('name' => $name);
		switch ($name) {
			case self::REQUEST_NAME_PROVIDER :
				// no params for provider
				break;
			case self::REQUEST_NAME_REQUEST_CODE :
				$games = $params['games'];
				if (!is_array($games)) {
					$games = array($games);
				}
				$gameIds = array();
				foreach ($games as $game) {
					$gameIds[] = $game->getId();
				}
				$data['games'] = $gameIds;
				$data['addAllowed'] = $params['addAllowed'];
				break;
			case self::REQUEST_NAME_TOURNAMENT :
				$data['tournament'] = $params->getId();
				break;
			case self::REQUEST_NAME_UPDATE_CODE :
				$data['game'] = $params['game']->getId();
				$data['addAllowed'] = $params['addAllowed'];
				break;
		}

		$request->setData(json_encode($data));
		$this->getObjectManager()->persist($request);
		$this->getObjectManager()->flush($request);
	}

	public function workQueue() {
		$repo = $this->getObjectManager()->getRepository(RiotApiRequest::class);
		$requests = $repo->findBy(array('requestType' => RiotApiRequest::REQUEST_TYPE_TOURNAMENT), array('id' => 'ASC'));
		foreach ($requests as $request) {/* @var $request RiotApiRequest */
			$data = json_decode($request->getData());
			$result = null;
			switch ($data->name) {
				case self::REQUEST_NAME_PROVIDER :
					$result = $this->requestProvider();
					break;
				case self::REQUEST_NAME_REQUEST_CODE :
					$gameIds = $data->games;
					if (!is_array($gameIds)) {
						continue; // invalid request
					}
					$gameRepo = $this->getObjectManager()->getRepository(Game::class);
					$games = array();
					foreach ($gameIds as $id) {
						$game = $gameRepo->find((int) $id);
						if ($game) {
							$games[] = $game;
						}
					}
					if (!isset($data->addAllowed)) {
						$data->addAllowed = false;
					}
					$result = $this->requestCodes($games, $data->addAllowed);
					break;
				case self::REQUEST_NAME_TOURNAMENT :
					$tournamentRepo = $this->getObjectManager()->getRepository(Tournament::class);
					$tournament = $tournamentRepo->find((int) $data->tournament);
					if (!$tournament) {
						continue;
					}
					$result = $this->requestTournament($tournament);
					break;
				case self::REQUEST_NAME_UPDATE_CODE :
					$gameRepo = $this->getObjectManager()->getRepository(Game::class);
					$game = $gameRepo->find((int) $data->game);
					if (!$game) {
						continue;
					}
					if (!isset($data->addAllowed)) {
						$data->addAllowed = false;
					}
					$result = $this->updateCode($game, $data->addAllowed);
					break;
			}
			if ($result === self::STATUS_CODE_RATE_LIMIT) {
				break;
			} else {
				// do not remove request from top of the queue if rate limit is exceeded
				// Note that this request is added by the method at the end of the queue already and 
				// will be executed a secod time eventually
				$this->getObjectManager()->remove($request);
			}
		}
		$this->getObjectManager()->flush();
	}

	/**
	 * Request an API endpoint with get, post, put or delete method and passed parameters. Returns null if no API Key is provided
	 * @param string $endpoint
	 * @param string $method Supports GET, POST, PUT and DELETE
	 * @param array $params
	 * @return array|null
	 */
	public function request($endpoint, $method, $params = array(), $GETParams = array()) {
		if ($this->rateLimitExceeded) {
			return array("statuscode" => self::STATUS_CODE_RATE_LIMIT, "content" => 'Rate limit exceeded');
		}
		$options = $this->getOptions();
		$url = $this->getBaseUrl($endpoint);
		if (!$options->getTournamentKey()) {
			return null;
		}

		$ch = null;
		$headers = array();
		$headers[] = 'X-Riot-Token: ' . $options->getTournamentKey();
		switch (strtolower($method)) {
			case 'get':
				$params = $params + $GETParams;
				$requestUrl = $url . '?' . http_build_query($params);
				$ch = curl_init($requestUrl);
				break;
			case 'post':
				$ch = curl_init($url . '?' . http_build_query($GETParams));
				$dataString = json_encode($params);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Content-Length: ' . strlen($dataString);
				break;
			case 'put':
				$ch = curl_init($url . '?' . http_build_query($GETParams));
				$dataString = json_encode($params);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Content-Length: ' . strlen($dataString);
				break;
			case 'delete':
				$ch = curl_init($url . '?' . http_build_query($GETParams));
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
				break;
		}

		if (!is_resource($ch) || get_resource_type($ch) !== 'curl') {
			return array("statuscode" => 405, "content" => 'Method not allowed');
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$output = curl_exec($ch);
		$httpcode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$res = array("statuscode" => $httpcode, "content" => $output);
		if ($res['statuscode'] === self::STATUS_CODE_RATE_LIMIT) {
			$this->rateLimitExceeded = true;
			var_dump($res);
		}
		return $res;
	}

}
