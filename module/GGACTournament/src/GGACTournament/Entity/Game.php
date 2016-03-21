<?php
namespace GGACTournament\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Game
 *
 * @ORM\Entity
 * @ORM\Table(name="game")
 */
class Game implements JsonSerializable
{
	const PICK_TYPE_TOURNAMENT_DRAFT = 'TOURNAMENT_DRAFT';
	const PICK_TYPE_BLIND_PICK = 'BLIND_PICK';
	const PICK_TYPE_DRAFT_MODE = 'DRAFT_MODE';
	const PICK_TYPE_ALL_RANDOM = 'ALL_RANDOM';
	
	const SPECTATOR_TYPE_NONE = 'NONE';
	const SPECTATOR_TYPE_LOBBYONLY = 'LOBBYONLY';
	const SPECTATOR_TYPE_ALL = 'ALL';
	
	const MAP_TYPE_SUMMONERS_RIFT = 'SUMMONERS_RIFT';
	const MAP_TYPE_TWISTED_TREELINE = 'TWISTED_TREELINE';
	const MAP_TYPE_CRYSTAL_SCAR = 'CRYSTAL_SCAR';
	const MAP_TYPE_HOWLING_ABYSS = 'HOWLING_ABYSS';
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Match")
	 * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
	 */
	protected $match;
 	
	/**
	 * @ORM\Column(name="`number`", type="integer");
	 */
	protected $number;
 	
	/**
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_blue_id", referencedColumnName="id")
	 */
	protected $teamBlue;
 	
	/**
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_purple_id", referencedColumnName="id")
	 */
	protected $teamPurple;
 	
	/**
	 * @ORM\Column(type="float", nullable=true);
	 */
	protected $pointsBlue;
 	
	/**
	 * @ORM\Column(type="float", nullable=true);
	 */
	protected $pointsPurple;
 	
	/**
	 * @ORM\Column(type="string", nullable=true);
	 */
	protected $meldungHome;
 	
	/**
	 * @ORM\Column(type="string", nullable=true);
	 */
	protected $meldungGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $anmerkungHome;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $anmerkungGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $screenHome;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $screenGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $gameLinkHome;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $gameLinkGuest;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $report;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $streamLink;
 	
	/**
	 * @ORM\Column(type="text", nullable=true);
	 */
	protected $tournamentCode;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $pickType;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $mapType;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $spectatorType;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return Match
	 */
	public function getMatch() {
		return $this->match;
	}

	/**
	 * @return int
	 */
	public function getNumber() {
		return $this->number;
	}
	
	/**
	 * @return Team
	 */
	public function getTeamBlue() {
		return $this->teamBlue;
	}

	/**
	 * @return Team
	 */
	public function getTeamPurple() {
		return $this->teamPurple;
	}

	/**
	 * @return float
	 */
	public function getPointsBlue() {
		return $this->pointsBlue;
	}

	/**
	 * @return float
	 */
	public function getPointsPurple() {
		return $this->pointsPurple;
	}

	/**
	 * @return string
	 */
	public function getMeldungHome() {
		return $this->meldungHome;
	}

	/**
	 * @return string
	 */
	public function getMeldungGuest() {
		return $this->meldungGuest;
	}

	/**
	 * @return string
	 */
	public function getAnmerkungHome() {
		return $this->anmerkungHome;
	}

	/**
	 * @return string
	 */
	public function getAnmerkungGuest() {
		return $this->anmerkungGuest;
	}

	/**
	 * @return string
	 */
	public function getScreenHome() {
		return $this->screenHome;
	}

	/**
	 * @return string
	 */
	public function getScreenGuest() {
		return $this->screenGuest;
	}

	/**
	 * @return string
	 */
	public function getGameLinkHome() {
		return $this->gameLinkHome;
	}

	/**
	 * @return string
	 */
	public function getGameLinkGuest() {
		return $this->gameLinkGuest;
	}

	/**
	 * @return string
	 */
	public function getReport() {
		return $this->report;
	}

	/**
	 * @return string
	 */
	public function getStreamLink() {
		return $this->streamLink;
	}

	/**
	 * @return string
	 */
	public function getTournamentCode() {
		return $this->tournamentCode;
	}
	
	/**
	 * @return string
	 */
	public function getPickType() {
		return $this->pickType;
	}

	/**
	 * @return string
	 */
	public function getMapType() {
		return $this->mapType;
	}

	/**
	 * @return string
	 */
	public function getSpectatorType() {
		return $this->spectatorType;
	}

	/**
	 * @param int $id
	 * @return Game
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @param Match $match
	 * @return Game
	 */
	public function setMatch($match) {
		$this->match = $match;
		return $this;
	}

	/**
	 * @param int $number
	 * @return Game
	 */
	public function setNumber($number) {
		$this->number = $number;
		return $this;
	}

	/**
	 * @param Team $teamBlue
	 * @return Game
	 */
	public function setTeamBlue($teamBlue) {
		$this->teamBlue = $teamBlue;
		return $this;
	}

	/**
	 * @param Team $teamPurple
	 * @return Game
	 */
	public function setTeamPurple($teamPurple) {
		$this->teamPurple = $teamPurple;
		return $this;
	}

	/**
	 * @param float $pointsBlue
	 * @return Game
	 */
	public function setPointsBlue($pointsBlue) {
		$this->pointsBlue = $pointsBlue;
		return $this;
	}

	/**
	 * @param float $pointsPurple
	 * @return Game
	 */
	public function setPointsPurple($pointsPurple) {
		$this->pointsPurple = $pointsPurple;
		return $this;
	}

	/**
	 * @param string $meldungHome
	 * @return Game
	 */
	public function setMeldungHome($meldungHome) {
		$this->meldungHome = $meldungHome;
		return $this;
	}

	/**
	 * @param string $meldungGuest
	 * @return Game
	 */
	public function setMeldungGuest($meldungGuest) {
		$this->meldungGuest = $meldungGuest;
		return $this;
	}

	/**
	 * @param string $anmerkungHome
	 * @return Game
	 */
	public function setAnmerkungHome($anmerkungHome) {
		$this->anmerkungHome = $anmerkungHome;
		return $this;
	}

	/**
	 * @param string $anmerkungGuest
	 * @return Game
	 */
	public function setAnmerkungGuest($anmerkungGuest) {
		$this->anmerkungGuest = $anmerkungGuest;
		return $this;
	}

	/**
	 * @param string $screenHome
	 * @return Game
	 */
	public function setScreenHome($screenHome) {
		$this->screenHome = $screenHome;
		return $this;
	}

	/**
	 * @param string $screenGuest
	 * @return Game
	 */
	public function setScreenGuest($screenGuest) {
		$this->screenGuest = $screenGuest;
		return $this;
	}

	/**
	 * @param string $gameLinkHome
	 * @return Game
	 */
	public function setGameLinkHome($gameLinkHome) {
		$this->gameLinkHome = $gameLinkHome;
		return $this;
	}

	/**
	 * @param string $gameLinkGuest
	 * @return Game
	 */
	public function setGameLinkGuest($gameLinkGuest) {
		$this->gameLinkGuest = $gameLinkGuest;
		return $this;
	}

	/**
	 * @param string $report
	 * @return Game
	 */
	public function setReport($report) {
		$this->report = $report;
		return $this;
	}

	/**
	 * @param string $streamLink
	 * @return Game
	 */
	public function setStreamLink($streamLink) {
		$this->streamLink = $streamLink;
		return $this;
	}

	/**
	 * @param string $tournamentCode
	 * @return Game
	 */
	public function setTournamentCode($tournamentCode) {
		$this->tournamentCode = $tournamentCode;
		return $this;
	}

	/**
	 * @param string $pickType
	 * @return Game
	 */
	public function setPickType($pickType) {
		$this->pickType = $pickType;
		return $this;
	}

	/**
	 * @param string $mapType
	 * @return Game
	 */
	public function setMapType($mapType) {
		$this->mapType = $mapType;
		return $this;
	}

	/**
	 * @param string $spectatorType
	 * @return Game
	 */
	public function setSpectatorType($spectatorType) {
		$this->spectatorType = $spectatorType;
		return $this;
	}

	/**
	 * Generates tournament codes
	 * @deprecated since version 1.0
	 * @return string
	 */
	public function generateTournamentCode(){
		
		// Keine Codes für Spielfrei
		if($this->getTeamBlue() == null || $this->getTeamPurple() == null)
			return;
		
		$tournamentName = $this->getMatch()->getRound()->getGroup()->getTournament()->getName();
		
		$url = 'pvpnet://lol/customgame/joinorcreate';
		$maps = array('map1' => "Summoners Rift", 'map10' => "Twisted Treeline", 'map8' => 'Crystal Scar', 'map12' => 'Howling Abyss');
		$picks = array('pick1' => 'Blind Pick', 'pick2' => "Draft Mode", 'pick4' => "All Random", 'pick6' => 'Tournament Draft');
		
		$url .= '/map1'; // map
		$url .= '/pick6'; // pick type
		$url .= '/team5'; // 5 Players per team
		$url .= '/specALL'; // allow spectate all (specLOBBY / specNONE for restriction)
		
		$bytes = openssl_random_pseudo_bytes(5);
		$password = bin2hex($bytes);
		//$password = "bbabababa";
		
		$data = array(
			"name" => $tournamentName.PHP_EOL.
						"Rd. ".$this->getMatch()->getRound()->getNumber(). ", Match ".$this->getMatch()->getNumber(). ", Spiel ".$this->getNumber().PHP_EOL.
						$this->getTeamBlue()->getName() . " - ".$this->getTeamPurple()->getName(),
			"extra" => $this->getId()."_".$this->getNumber(),
			"password" => $password,
			"report" => "http://lol.fsmpi.rwth-aachen.de/gamereport.html"
		);
		
		$code = $url.'/'.base64_encode(json_encode($data));
		$this->setTournamentCode($code);
	}
	
	/**
	 * Returns json String
	 * @return string
	 */
	public function toJson(){
		$data = $this->jsonSerialize();
		return Json::encode($data, true, array('silenceCyclicalExceptions' => true));
	}
	
	/**
	 * Returns data to show in json
	 * @return array
	 */
	public function jsonSerialize(){
		$data = array(
			"id" => $this->getId(),
			"anmerkungHome" => $this->getAnmerkungHome(),
			"anmerkungGuest" => $this->getAnmerkungGuest(),
			"gameLinkGuest" => $this->getGameLinkGuest(),
			"gameLinkHome" => $this->getGameLinkHome(),
			"meldungGuest" => $this->getMeldungGuest(),
			"meldungHome" => $this->getMeldungHome(),
			"number" => $this->getNumber(),
			"pointsBlue" => $this->getPointsBlue(),
			"pointsPurple" => $this->getPointsPurple(),
			"report" => $this->getReport(),
			"screenGuest" => $this->getScreenGuest(),
			"screenHome" => $this->getScreenHome(),
			"streamLink" => $this->getStreamLink(),
			"teamBlue" => $this->getTeamBlue(),
			"teamPurple" => $this->getTeamPurple(),
		);
		return $data;
	}
}