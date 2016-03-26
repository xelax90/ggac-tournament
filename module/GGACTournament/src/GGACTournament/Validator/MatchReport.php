<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GGACTournament\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Description of MatchReport
 *
 * @author schurix
 */
class MatchReport extends AbstractValidator{
	const MESSAGE_NOT_ENOUGH = 'notEnough';
	const MESSAGE_TOO_MANY = 'tooMany';
	
	protected $messageTemplates = array(
		self::MESSAGE_NOT_ENOUGH => 'Nicht genügend Spiele gespielt',
		self::MESSAGE_TOO_MANY => 'Zu viele Spiele gespielt',
	);
	
	/**
	 * Options for the between validator
	 *
	 * @var array
	 */
	protected $options = array(
		'collection_key' => 'games', // key of registrations collection. TODO: use path to email instead of key to collection
		'attribute_key' => 'meldungHome', // key of element that will be checked
	);
	
	
	public function isValid($value, $context = null) {
		if(!$context){
			return true;
		}
		
		$collectionKey = $this->getOption('collection_key');
		$attributeKey = $this->getOption('attribute_key');
		
		// TODO provide path to all email fields instead of anmldung
		if(!empty($collectionKey) && !isset($context[$collectionKey])){
			throw new \Zend\Validator\Exception\InvalidArgumentException(sprintf('Collection key "%s" not set', $collectionKey));
		}
		
		$count = count($context[$collectionKey]);
		$maxPoints = ceil($count / 2);
		$sumH = 0;
		$sumG = 0;
		foreach($context[$collectionKey] as $game){
			$meldung = $game[$attributeKey];
			switch($meldung){
				case '1-0' : 
				case '+--' :
					$sumH++; 
					break;
				case '0-1' : 
				case '--+' :
					$sumG++; 
					break;
			}
		}
		
		if($sumG < $count / 2 && $sumH < $count / 2){
			$this->error(static::MESSAGE_NOT_ENOUGH);
			return false;
		}
		
		if($sumG > $maxPoints || $sumH > $maxPoints){
			$this->error(static::MESSAGE_TOO_MANY);
			return false;
		}
		return true;
	}
}
