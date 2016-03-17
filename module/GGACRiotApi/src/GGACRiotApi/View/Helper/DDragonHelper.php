<?php
namespace GGACRiotApi\View\Helper;

use Zend\View\Helper\AbstractHelper;
use GGACRiotApi\Cache\ApiCache;

class DDragonHelper extends AbstractHelper {
	protected $version = '6.5.1';
	protected $base = 'http://ddragon.leagueoflegends.com/cdn/';
	protected $profileIconPath = 'img/profileicon/';
	
	protected static $instance;
	
	public function __invoke(){
		return $this;
	}
	
	public function version(){
		return $this->version;
	}
	
	public function setVersion($version){
		$this->version = $version;
		return $this;
	}
	
	public function setBaseUrl($base) {
		$this->base = $base;
		return $this;
	}
		
	public function baseUrl(){
		return $this->base . $this->version()."/";
	}
	
	public function profileIcon($iconId){
		return $this->baseUrl() . $this->profileIconPath . $iconId . ".png";
	}
}
