<?php
namespace GGACTournament\Entity;

use GGACTournament\Tournament\ApiData\Data as ApiData;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Registration
 *
 * @ORM\Entity(repositoryClass="GGACTournament\Model\RegistrationRepository")
 * @ORM\Table(name="registration")
 */
class Registration implements JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
	 * @ORM\Column(type="string", nullable=true);
	 */
	protected $teamName;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $name;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $email;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $facebook;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $otherContact;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $summonerName;
 	
	/**
	 * @ORM\Column(type="integer", nullable=true);
	 */
	protected $summonerId;
 	
	/**
	 * @ORM\Column(type="integer", nullable=true);
	 */
	protected $isSub;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $anmerkung;
 	
	/**
	 * @ORM\Column(type="string", nullable=true);
	 */
	protected $icon;
	
	/**
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="registrations")
	 * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
	 */
	protected $tournament;

	/**
     * @ORM\OneToOne(targetEntity="Player", mappedBy="registration")
	 */
	protected $player;
	
	/**
	 * @var ApiData
	 */
	protected $data;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTeamName() {
		return $this->teamName;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * @return string
	 */
	public function getFacebook() {
		return $this->facebook;
	}
	
	/**
	 * @return string
	 */
	public function getOtherContact() {
		return $this->otherContact;
	}
	
	/**
	 * @return string
	 */
	public function getSummonerName() {
		return $this->summonerName;
	}
	
	/**
	 * @return int
	 */
	public function getSummonerId() {
		return $this->summonerId;
	}

	/**
	 * @return boolean
	 */
	public function getIsSub() {
		return $this->isSub;
	}

	/**
	 * @return string
	 */
	public function getAnmerkung() {
		return $this->anmerkung;
	}
	
	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->icon;
	}
	
	/**
	 * @return Tournament
	 */
	public function getTournament() {
		return $this->tournament;
	}
	
	/**
	 * @return Player
	 */
	public function getPlayer() {
		return $this->player;
	}
	
	/**
	 * @return ApiData;
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * @param int $id
	 * @return Registration
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * @param string $teamName
	 * @return Registration
	 */
	public function setTeamName($teamName) {
		$this->teamName = $teamName;
		return $this;
	}
	
	/**
	 * @param string $name
	 * @return Registration
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	/**
	 * @param string $email
	 * @return Registration
	 */
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}
	
	/**
	 * @param string $facebook
	 * @return Registration
	 */
	public function setFacebook($facebook) {
		$this->facebook = $facebook;
		return $this;
	}
	
	/**
	 * @param string $otherContact
	 * @return Registration
	 */
	public function setOtherContact($otherContact) {
		$this->otherContact = $otherContact;
		return $this;
	}
	
	/**
	 * @param string $summonerName
	 * @return Registration
	 */
	public function setSummonerName($summonerName) {
		$this->summonerName = $summonerName;
		return $this;
	}
	
	/**
	 * @param int $summonerId
	 * @return Registration
	 */
	public function setSummonerId($summonerId) {
		$this->summonerId = $summonerId;
		return $this;
	}

	/**
	 * @param boolean $isSub
	 * @return Registration
	 */
	public function setIsSub($isSub) {
		$this->isSub = $isSub;
		return $this;
	}
	
	/**
	 * @param string $anmerkung
	 * @return Registration
	 */
	public function setAnmerkung($anmerkung) {
		$this->anmerkung = $anmerkung;
		return $this;
	}
	
	/**
	 * @param string $icon
	 * @return Registration
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
		return $this;
	}

	/**
	 * @param Tournament $tournament
	 * @return Registration
	 */
	public function setTournament($tournament) {
		$this->tournament = $tournament;
		return $this;
	}
	
	/**
	 * @param Player $player
	 * @return Registration
	 */
	public function setPlayer($player) {
		$this->player = $player;
		return $this;
	}
	
	/**
	 * @param ApiData $data
	 * @return Registration
	 */
	public function setData(ApiData $data) {
		$this->data = $data;
		return $this;
	}
	
	/**
	 * Returns score computed by data.
	 * @param boolean $refresh Set true to force recomputation
	 * @return int
	 */
	public function getScore($refresh = false){
		if(!$this->getData()){
			return 0;
		}
		return $this->getData()->getScore($refresh);
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
			"teamName" => $this->getTeamName(),
			"name" => $this->getName(),
			"email" => $this->getEmail(),
			"facebook" => $this->getFacebook(),
			"otherContact" => $this->getOtherContact(),
			"summonerName" => $this->getSummonerName(),
			"summonerId" => $this->getSummonerId(),
			"isSub" => $this->getIsSub(),
			"anmerkung" => $this->getAnmerkung(),
			"icon" => $this->getIcon(),
			"tournament" => $this->getTournament(),
		);
		return $data;
	}
}