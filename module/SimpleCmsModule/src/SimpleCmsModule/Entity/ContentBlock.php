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

namespace SimpleCmsModule\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Json\Json;
use JsonSerializable;

/**
 * ContentBlock Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="contentblock")
 */
class ContentBlock implements JsonSerializable{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @ORM\Column(type="string")
	 */
	protected $position;
	
	/**
	 * @ORM\Column(type="integer")
	 */
	protected $ordering;
	
	/**
	 * @ORM\Column(type="text")
	 */
	protected $title;
	
	/**
	 * @ORM\Column(type="text")
	 */
	protected $content;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getPosition() {
		return $this->position;
	}

	/**
	 * @return int
	 */
	public function getOrdering() {
		return $this->ordering;
	}
	
	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param int $id
	 * @return ContentBlock
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * @param string $position
	 * @return ContentBlock
	 */
	public function setPosition($position) {
		$this->position = $position;
		return $this;
	}

	/**
	 * @param int $ordering
	 * @return ContentBlock
	 */
	public function setOrdering($ordering) {
		$this->ordering = $ordering;
		return $this;
	}
	
	/**
	 * @param string $title
	 * @return ContentBlock
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 * @param string $content
	 * @return ContentBlock
	 */
	public function setContent($content) {
		$this->content = $content;
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
			'id' => $this->getId(),
			'position' => $this->getPosition(),
			'ordering' => $this->getOrdering(),
			'title' => $this->getTitle(),
			'content' => $this->getContent(),
		);
	}

}
