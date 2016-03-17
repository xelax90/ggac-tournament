<?php
namespace GGACTournament\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * A Player
 *
 * @ORM\Entity
 * @ORM\Table(name="warning")
 * @property int $id
 * @property Player $player
 * @property Team $team
 * @property string $comment
 */
class Warning implements JsonSerializable
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $team;
 	
	/**
     * @ORM\ManyToOne(targetEntity="Player")
	 * @ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $player;
 	
	/**
	 * @ORM\Column(type="text");
	 */
	protected $comment;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return Team
	 */
	public function getTeam() {
		return $this->team;
	}

	/**
	 * @return Player
	 */
	public function getPlayer() {
		return $this->player;
	}

	/**
	 * @return string
	 */
	public function getComment() {
		return $this->comment;
	}
	
	/**
	 * @param int $id
	 * @return Warning
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @param Team $team
	 * @return Warning
	 */
	public function setTeam($team) {
		$this->team = $team;
		return $this;
	}

	/**
	 * @param Player $player
	 * @return Warning
	 */
	public function setPlayer($player) {
		$this->player = $player;
		return $this;
	}

	/**
	 * @param string $comment
	 * @return Warning
	 */
	public function setComment($comment) {
		$this->comment = $comment;
		return $this;
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
			"team" => $this->getTeam(),
			"player" => $this->getPlayer(),
			"comment" => $this->getComment(),
		);
		return $data;
	}
}