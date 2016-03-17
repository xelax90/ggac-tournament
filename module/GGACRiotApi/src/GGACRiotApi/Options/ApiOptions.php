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

namespace GGACRiotApi\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Description of ApiOptions
 *
 * @author schurix
 */
class ApiOptions extends AbstractOptions{
	protected $key = '';
	protected $region = 'euw';
	protected $maxRequests = 20;
	protected $protocol = 'https';
	protected $url = "api.pvp.net";
	
	public function getKey() {
		return $this->key;
	}

	public function getRegion() {
		return $this->region;
	}

	public function getMaxRequests() {
		return $this->maxRequests;
	}

	public function getProtocol() {
		return $this->protocol;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setKey($key) {
		$this->key = $key;
		return $this;
	}

	public function setRegion($region) {
		$this->region = $region;
		return $this;
	}

	public function setMaxRequests($maxRequests) {
		$this->maxRequests = $maxRequests;
		return $this;
	}

	public function setProtocol($protocol) {
		$this->protocol = $protocol;
		return $this;
	}

	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}
}
