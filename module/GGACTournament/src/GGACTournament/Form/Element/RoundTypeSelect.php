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

namespace GGACTournament\Form\Element;

use Zend\Form\Element\Select;

/**
 * Description of TournamentStateSelect
 *
 * @author schurix
 */
class RoundTypeSelect extends Select{
	
	public function setValue($value) {
		parent::setValue($value);
		if(is_array($value)){
			$newOptions = array();
			$valueOptions = $this->getValueOptions();
			foreach ($valueOptions as $k => $v) {
				if(!in_array($k, $value)){
					$newOptions[$k] = $v;
				}
			}
			$keys = array_keys($valueOptions);
			foreach($value as $v){
				if(in_array($v, $keys)){
					$newOptions[$v] = $valueOptions[$v];
				}
			}
			$this->valueOptions = $newOptions;
		}
	}
	
}
