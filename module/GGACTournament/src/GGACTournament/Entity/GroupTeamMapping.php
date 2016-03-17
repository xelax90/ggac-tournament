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

namespace GGACTournament\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * Group - Team mapping that provides seed property
 *
 * @ORM\Entity
 * @ORM\Table(name="group_team_mapping", indexes={@ORM\Index(name="group_idx", columns={"group_id"}), @ORM\Index(name="team_idx", columns={"team_id"})})
 */
class GroupTeamMapping implements JsonSerializable{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Group")
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
	 */
	protected $group;
	
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
	 */
	protected $team;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $seed;
	
	/**
	 * @return Group
	 */
	public function getGroup() {
		return $this->group;
	}
	
	/**
	 * @return Team
	 */
	public function getTeam() {
		return $this->team;
	}
	
	/**
	 * @return int
	 */
	public function getSeed() {
		return $this->seed;
	}
	
	/**
	 * @param Group $group
	 * @return GroupTeamMapping
	 */
	public function setGroup($group) {
		$this->group = $group;
		return $this;
	}

	/**
	 * @param Team $team
	 * @return GroupTeamMapping
	 */
	public function setTeam($team) {
		$this->team = $team;
		return $this;
	}

	/**
	 * @param int $seed
	 * @return GroupTeamMapping
	 */
	public function setSeed($seed) {
		$this->seed = $seed;
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
	public function jsonSerialize() {
		return array(
			'group_id' => $this->getGroup()->getId(),
			'team_id' => $this->getTeam()->getId(),
			'seed' => $this->getSeed(),
		);
	}

}
