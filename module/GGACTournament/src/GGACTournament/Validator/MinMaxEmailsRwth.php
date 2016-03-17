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

namespace GGACTournament\Validator;
use Zend\Validator\Exception;

/**
 * Description of MinMaxEmailsRWTH
 *
 * @author schurix
 */
class MinMaxEmailsRwth extends MinMaxValuesMatchingCallback{
	/** @var EmailIsRwth */
	protected $emailValidator;
	
	public function getEmailValidator() {
		return $this->emailValidator;
	}

	public function setEmailValidator(EmailIsRwth $emailValidator) {
		$this->emailValidator = $emailValidator;
		return $this;
	}

	protected function getCallback() {
		return array($this, 'checkEmail');
	}
	
	public function checkEmail($value){
		$validator = $this->getEmailValidator();
		if(!$validator){
			throw new Exception\InvalidArgumentException('Email validator not set');
		}
		return $validator->isValid($value);
	}
}
