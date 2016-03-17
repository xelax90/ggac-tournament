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

use Zend\Validator\AbstractValidator;

/**
 * Description of EmailIsRwth
 *
 * @author schurix
 */
class EmailIsRwth extends AbstractValidator{
	
    const NOT_RWTH   = 'notRwth';
    const TIM_ID     = 'timId';
    const INVALID    = 'emailInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = [
        self::NOT_RWTH   => "The email must be from RWTH-Aachen",
        self::TIM_ID     => "TIM ID passed instead of email",
        self::INVALID    => "Invalid type given. String expected",
    ];

    /**
     * Returns true if and only if $value corresponds to an RWTH- or FH-Aachen email adress
     *
     * @param  string $value
     * @return bool
     */
    public function isValid($value) {
		if (!is_string($value)) {
			$this->error(self::INVALID);
			return false;
		}

		$this->setValue((string) $value);

		$rwthRegex = '/fh-aachen.de$|rwth-aachen.de$/';
		if(!preg_match($rwthRegex, strtolower($this->getValue()))){
			$this->error(self::NOT_RWTH);
			return false;
		}

		return true;
    }

}
