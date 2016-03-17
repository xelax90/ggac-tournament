<?php
namespace GGACTournament\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Player
 *
 * @ORM\Entity(repositoryClass="GGACTournament\Model\PlayerRepository")
 * @ORM\Table(name="player")
 */
class Player implements JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\OneToOne(targetEntity="Registration", inversedBy="player", cascade={"persist"})
	 * @ORM\JoinColumn(name="registration_id", referencedColumnName="id")
	 */
	protected $registration;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $team;
 	
	/**
	 * @ORM\Column(type="boolean");
	 */
	protected $isCaptain;
 	
	/**
	 * @ORM\ManyToOne(targetEntity="SkelletonApplication\Entity\User",inversedBy="players")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="SET NULL")
	 */
	protected $user;
	
	/**
     * @ORM\OneToMany(targetEntity="Warning", mappedBy="player")
	 */
	protected $warnings;
	
	/** @var int */
	protected $score;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return Registration
	 */
	public function getRegistration() {
		return $this->registration;
	}

	/**
	 * @return Team
	 */
	public function getTeam() {
		return $this->team;
	}

	/**
	 * @return boolean
	 */
	public function getIsCaptain() {
		return $this->isCaptain;
	}

	/**
	 * @return \SkelletonApplication\Entity\User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @return array
	 */
	public function getWarnings() {
		return $this->warnings;
	}
	
	/**
	 * @param int $id
	 * @return Player
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @param Registration $registration
	 * @return Player
	 */
	public function setRegistration($registration) {
		$this->registration = $registration;
		return $this;
	}

	/**
	 * @param Team $team
	 * @return Player
	 */
	public function setTeam($team) {
		$this->team = $team;
		return $this;
	}

	/**
	 * @param boolean $isCaptain
	 * @return Player
	 */
	public function setIsCaptain($isCaptain) {
		$this->isCaptain = $isCaptain;
		return $this;
	}

	/**
	 * @param \SkelletonApplication\Entity\User $user
	 * @return Player
	 */
	public function setUser($user) {
		$this->user = $user;
		return $this;
	}

	/**
	 * @param array $warnings
	 * @return Player
	 */
	public function setWarnings($warnings) {
		$this->warnings = $warnings;
		return $this;
	}

	/**
	 * Returns the score computed by registration
	 * @param boolean $refresh
	 * @return int
	 */
	public function getScore($refresh = false){
		return $this->getRegistration()->getScore($refresh);
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
			"registration" => $this->getRegistration(),
			"team" => $this->getTeam(),
			"isCaptain" => $this->getIsCaptain(),
			"warnings" => $this->getWarnings(),
		);
		return $data;
	}
}