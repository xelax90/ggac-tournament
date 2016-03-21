<?php
namespace GGACTournament\Entity;

use GGACTournament\Tournament\RoundCreator\AlreadyPlayedInterface;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Tournament
 *
 * @ORM\Entity
 * @ORM\Table(name="tournament")
 * @property int $id
 * @property int $name
 */
class Tournament implements JsonSerializable, AlreadyPlayedInterface
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
	 * @ORM\Column(type="string");
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $apiId;
	
	/**
	 * @ORM\OneToMany(targetEntity="Registration", mappedBy="tournament");
	 */
	protected $registrations;
	
	/**
	 * @ORM\OneToMany(targetEntity="TournamentPhase", mappedBy="tournament")
	 * @ORM\OrderBy({"number" = "DESC"})
	 */
	protected $phases;
	
	/**
	 * @ORM\OneToMany(targetEntity="Team", mappedBy="tournament")
	 * @ORM\OrderBy({"number" = "ASC"})
	 */
	protected $teams;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $rulesFile;
	
	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $announcementFile;
	
	/**
	 * Minimal number of subs created by TeamMatcher
	 * @ORM\Column(type="integer")
	 */
	protected $minimumSubs;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $registrationTeamSize;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $registrationSingleRequireRWTH;
	
	/**
	 * @ORM\Column(type="float")
	 */
	protected $registrationTeamMinRWTH;
	
	/**
	 * @ORM\Column(type="float")
	 */
	protected $registrationTeamMaxNotRWTH;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $registrationTeamMaxMembers;
	
	protected $subs;
	
	/**
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getApiId() {
		return $this->apiId;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}
	
	/**
	 * @return string
	 */
	public function getRegistrations(){
		return $this->registrations;
	}
	
	/**
	 * @return array
	 */
	public function getPhases(){
		return $this->phases;
	}
	
	/**
	 * @return array
	 */
	public function getTeams() {
		return $this->teams;
	}

	/**
	 * @return string
	 */
	public function getRulesFile() {
		return $this->rulesFile;
	}

	/**
	 * @return string
	 */
	public function getAnnouncementFile() {
		return $this->announcementFile;
	}

	/**
	 * @return int
	 */
	public function getMinimumSubs() {
		return $this->minimumSubs;
	}

	/**
	 * @return int
	 */
	public function getRegistrationTeamSize() {
		return $this->registrationTeamSize;
	}
	
	/**
	 * @return boolean
	 */
	public function getRegistrationSingleRequireRWTH() {
		return $this->registrationSingleRequireRWTH;
	}

	/**
	 * @return float
	 */
	public function getRegistrationTeamMinRWTH() {
		return $this->registrationTeamMinRWTH;
	}

	/**
	 * @return float
	 */
	public function getRegistrationTeamMaxNotRWTH() {
		return $this->registrationTeamMaxNotRWTH;
	}

	/**
	 * @return int
	 */
	public function getRegistrationTeamMaxMembers() {
		return $this->registrationTeamMaxMembers;
	}
	
	/** 
	 * @param int $id
	 * @return Tournament
	 */
	public function setId($id){
		$this->id = $id;
		return $this;
	}
	
	/** 
	 * @param string $apiId
	 * @return Tournament
	 */
	public function setApiId($apiId) {
		$this->apiId = $apiId;
		return $this;
	}

	/** 
	 * @param string $name
	 * @return Tournament
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}
	
	/**
	 * @param aray $registrations
	 * @return Tournament
	 */
	public function setRegistrations($registrations) {
		$this->registrations = $registrations;
		return $this;
	}

	/**
	 * @param aray $phases
	 * @return Tournament
	 */
	public function setPhases($phases) {
		$this->phases = $phases;
		return $this;
	}
	
	/**
	 * @param aray $teams
	 * @return Tournament
	 */
	public function setTeams($teams) {
		$this->teams = $teams;
		return $this;
	}

	/** 
	 * @param string|array $rulesFile
	 * @return Tournament
	 */
	public function setRulesFile($rulesFile) {
		if(!is_array($rulesFile) || $rulesFile['error'] !== UPLOAD_ERR_NO_FILE){
			if(is_array($rulesFile)){
				$this->rulesFile = preg_replace('!^/?public!', '', $rulesFile['tmp_name']);;
			} else {
				$this->rulesFile = $rulesFile;
			}
		}
		return $this;
	}

	/** 
	 * @param string|array $announcementFile
	 * @return Tournament
	 */
	public function setAnnouncementFile($announcementFile) {
		if(!is_array($announcementFile) || $announcementFile['error'] !== UPLOAD_ERR_NO_FILE){
			if(is_array($announcementFile)){
				$this->announcementFile = preg_replace('!^/?public!', '', $announcementFile['tmp_name']);;
			} else {
				$this->announcementFile = $announcementFile;
			}
		}
		return $this;
	}

	/** 
	 * @param int $minimumSubs
	 * @return Tournament
	 */
	public function setMinimumSubs($minimumSubs) {
		$this->minimumSubs = $minimumSubs;
		return $this;
	}
	
	/**
	 * @param type $registrationTeamSize
	 * @return Tournament
	 */
	public function setRegistrationTeamSize($registrationTeamSize) {
		$this->registrationTeamSize = $registrationTeamSize;
		return $this;
	}

	/** 
	 * @param boolean $registrationSingleRequireRWTH
	 * @return Tournament
	 */
	public function setRegistrationSingleRequireRWTH($registrationSingleRequireRWTH) {
		$this->registrationSingleRequireRWTH = $registrationSingleRequireRWTH;
		return $this;
	}

	/** 
	 * @param float $registrationTeamMinRWTH
	 * @return Tournament
	 */
	public function setRegistrationTeamMinRWTH($registrationTeamMinRWTH) {
		$this->registrationTeamMinRWTH = $registrationTeamMinRWTH;
		return $this;
	}

	/** 
	 * @param float $registrationTeamMaxNotRWTH
	 * @return Tournament
	 */
	public function setRegistrationTeamMaxNotRWTH($registrationTeamMaxNotRWTH) {
		$this->registrationTeamMaxNotRWTH = $registrationTeamMaxNotRWTH;
		return $this;
	}

	/** 
	 * @param int $registrationTeamMaxMembers
	 * @return Tournament
	 */
	public function setRegistrationTeamMaxMembers($registrationTeamMaxMembers) {
		$this->registrationTeamMaxMembers = $registrationTeamMaxMembers;
		return $this;
	}

	/**
	 * Returns array of subs (e.g. players without a team) ordered by score
	 * @param boolean $refresh Set true to force recomputation
	 * @return array
	 */
	public function getSubs($refresh = false){
		if(null === $this->subs || $refresh){
			$this->subs = array();
			foreach($this->getRegistrations() as $registration){
				/* @var $registration Registration */
				if($registration->getPlayer() && !$registration->getPlayer()->getTeam()){
					$this->subs[] = $registration->getPlayer();
				}
			}
			usort($this->subs, function($a, $b){return $b->getScore(true) - $a->getScore(true);});
		}
		return $this->subs;
	}
	
	/**
	 * Returns true if $t1 and $t2 have already played against each other in this tournament
	 * @param Team $t1
	 * @param Team $t2
	 * @return boolean
	 */
	public function alreadyPlayed(Team $t1 = null, Team $t2 = null){
		// check if $t1 and $t2 played in any phase
		foreach($this->getPhases() as $phase){
			if($phase->alreadyPlayed($t1, $t2)){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns the currently active phase
	 * @return TournamentPhase
	 */
	public function getCurrentPhase(){
		$phases = $this->getPhases();
		
		$maxPhase = null;
		/* @var $maxPhase TournamentPhase */
		foreach($phases as $phase){
			/* @var $phase TournamentPhase */
			if($maxPhase == null || $maxPhase->getNumber() < $phase->getNumber()){
				$maxPhase = $phase;
			}
		}
		
		return $maxPhase;
	}

	/**
	 * Returns the tournament phase with highest number, but lower than $phase
	 * 
	 * @param TournamentPhase $phase
	 * @return TournamentPhase
	 */
	public function getPreviousPhase(TournamentPhase $phase = null){
		if($phase === null){
			$phase = $this->getCurrentPhase();
		}
		
		$phases = $this->getPhases();
		
		$maxPhase = null;
		/* @var $maxPhase TournamentPhase */
		foreach($phases as $currPhase){
			/* @var $currPhase TournamentPhase */
			if(
					($maxPhase == null || $maxPhase->getNumber() < $currPhase->getNumber()) && // bigger than $maxPhase
					($currPhase->getNumber() < $phase->getNumber()) // smaller than $phase
			){
				$maxPhase = $currPhase;
			}
		}
		
		return $maxPhase;
	}
	
	public function getMaxTeamNumber(){
		$maxNumber = 0;
		foreach($this->getTeams() as $team){
			/* @var $team Team */
			if(!$maxNumber || $maxNumber < $team->getNumber()){
				$maxNumber = $team->getNumber();
			}
		}
		return $maxNumber;
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
			"apiId" => $this->getApiId(),
			"name" => $this->getName(),
			"announcementFile" => $this->getAnnouncementFile(),
			"minimumSubs" => $this->getMinimumSubs(),
			"registrationSingleRequireRWTH" => $this->getRegistrationSingleRequireRWTH(),
			"registrationTeamMaxMembers" => $this->getRegistrationTeamMaxMembers(),
			"registrationTeamMaxNotRWTH" => $this->getRegistrationTeamMaxNotRWTH(),
			"registrationTeamMinRWTH" => $this->getRegistrationTeamMinRWTH(),
			"rulesFile" => $this->getRulesFile(),
		);
		return $data;
	}
}
