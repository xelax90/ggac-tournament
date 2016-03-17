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

use DoctrineModule\Validator\UniqueObject;
use GGACTournament\Tournament\ProviderAwareInterface;
use GGACTournament\Tournament\ProviderAwareTrait;
use Zend\Validator\Exception;
use GGACTournament\Tournament\Provider;

/**
 * Description of UniqueObjectInTournament
 *
 * @author schurix
 */
class UniqueObjectInTournament extends UniqueObject implements ProviderAwareInterface{
	use ProviderAwareTrait;
	
	public function __construct(array $options) {
		parent::__construct($options);
		
        if (!isset($options['tournament_provider']) || !$options['tournament_provider'] instanceof Provider) {
            if (!array_key_exists('tournament_provider', $options)) {
                $provided = 'nothing';
            } else {
                if (is_object($options['tournament_provider'])) {
                    $provided = get_class($options['tournament_provider']);
                } else {
                    $provided = getType($options['tournament_provider']);
                }
            }

            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Option "tournament_provider" is required and must be an instance of'
                    . ' '.Provider::class.', %s given',
                    $provided
                )
            );
        }
		$this->setTournamentProvider($options['tournament_provider']);
	}
	
	protected function cleanSearchValue($value) {
		$value = parent::cleanSearchValue($value);
		$value['tournament'] = $this->getTournamentProvider()->getTournament();
		return $value;
	}
	
	protected function getExpectedIdentifiers($context = null) {
		try {
			return parent::getExpectedIdentifiers($context);
		} catch (\Exception $exc) {
		}
		return array_fill_keys($this->getIdentifiers(), 0);
	}
}
