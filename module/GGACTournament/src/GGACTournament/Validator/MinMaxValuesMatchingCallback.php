<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GGACTournament\Validator;

use Zend\Validator\AbstractValidator;

/**
 * Description of MinMaxEmailsMatchingCallback
 *
 * @author schurix
 */
class MinMaxValuesMatchingCallback extends AbstractValidator{
	const MESSAGE_NOT_ENOUGH = 'notEnough';
	const MESSAGE_TOO_MANY = 'tooMany';
	
	protected $messageTemplates = array(
		self::MESSAGE_NOT_ENOUGH => 'Nicht genügend passende E-Mail Adressen gefunden',
		self::MESSAGE_TOO_MANY => 'Zu viele passende E-Mail Adressen gefunden',
	);
	
	/**
	 * Options for the between validator
	 *
	 * @var array
	 */
	protected $options = array(
		'ignore_empty' => true, // Whether to ignore empty e-mails or count them according to callback return
		'collection_key' => 'registrations', // key of registrations collection. TODO: use path to email instead of key to collection
		'attribute_key' => 'email', // key of element that will be checked
		'callback' => false,
		'min'       => 0,
		'max'       => PHP_INT_MAX,
	);
	
	
	public function isValid($value, $context = null) {
		if(!$context){
			return true;
		}
		
		$collectionKey = $this->getOption('collection_key');
		$attributeKey = $this->getOption('attribute_key');
		$callback = $this->getCallback();
		$ignoreEmpty = !!$this->getOption('ignore_empty');
		$min = $this->getOption('min');
		$max = $this->getOption('max');
		
		if(!is_callable($callback)){
			throw new \Zend\Validator\Exception\InvalidArgumentException('No valid callback provided');
		}
		
		// TODO provide path to all email fields instead of anmldung
		if(!empty($collectionKey) && !isset($context[$collectionKey])){
			throw new \Zend\Validator\Exception\InvalidArgumentException(sprintf('Collection key "%s" not set', $collectionKey));
		}
		
		$positives = 0;
		$negatives = 0;
		$notEmpty = 0;
		
		if(empty($collectionKey)){
			// allow empty key for single fieldset validation
			$fieldsets = array($context);
		} else {
			$fieldsets = $context[$collectionKey];
		}
		foreach($fieldsets as $k => $registration){
			$isMatching = call_user_func($callback, $registration[$attributeKey]);
			$isEmpty = empty($registration[$attributeKey]) && $ignoreEmpty;
			if($isMatching){
				if(!$isEmpty){
					$positives++;
				}
			} elseif(!$isEmpty){
				$negatives++;
			}
			if(!$isEmpty){
				$notEmpty++;
			}
		}
		
		if((is_int($min) || ((int)$min == $min)) && $positives < $min){
			$this->error(static::MESSAGE_NOT_ENOUGH);
			return false;
		} elseif(is_float($min) && ((int)$min != $min) && $positives/$notEmpty < $min){
			$this->error(static::MESSAGE_NOT_ENOUGH);
			return false;
		}
		
		if((is_int($max) || ((int) $max == $max) )&& $positives > $max){
			$this->error(static::MESSAGE_TOO_MANY);
			return false;
		} elseif(is_float($max) && ((int)$max != $max) && $positives/$notEmpty > $max){
			$this->error(static::MESSAGE_TOO_MANY);
			return false;
		}
		
		return true;
	}
	
	protected function getCallback(){
		$callback = $this->getOption('callback');
		return $callback;
	}
}
