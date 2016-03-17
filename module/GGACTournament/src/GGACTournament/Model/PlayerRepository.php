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

namespace GGACTournament\Model;

use Doctrine\ORM\EntityRepository;

/**
 * Repository for Player entity
 *
 * @author schurix
 */
class PlayerRepository extends EntityRepository{
	
	public function getPlayerForUser($user, $tournament){
		$query = $this->createQueryBuilder('p')
			->join('p.registration', 'a')
			->andWhere('p.user = ?1')
			->andWhere('a.tournament = ?2')
			->setParameter(1, $user)
			->setParameter(2, $tournament);
		return $query->getQuery()->getResult();
	}
	
	public function getPlayersForUser($user){
		return $this->findBy(array(
			'user' => $user
		));
	}
	
	public function getSubsForTournament($tournament){
		$query = $this->createQueryBuilder('p')
			->join('p.registration', 'a')
			->andWhere('p.team IS NULL')
			->andWhere('a.tournament = ?1')
			->setParameter(1, $tournament)
			->orderBy('a.summonerName', 'ASC');
		return $query->getQuery()->getResult();
	}
	
	public function getPlayersForTournament($tournament){
		$query = $this->createQueryBuilder('p')
			->join('p.registration', 'a')
			->andWhere('a.tournament = ?1')
			->setParameter(1, $tournament)
			->orderBy('a.summonerName', 'ASC');
		return $query->getQuery()->getResult();
	}
	
	public function getPlayerByEmail($mail, $tournament){
		$query = $this->createQueryBuilder('p')
			->join('p.registration', 'a')
			->andWhere('a.tournament = ?1')
			->andWhere('a.email = ?2')
			->setParameter(1, $tournament)
			->setParameter(2, $mail);
		return $query->getQuery()->getResult();
	}
	
	public function getPlayerBySummonerName($summonerName, $tournament){
		$query = $this->createQueryBuilder('p')
			->join('p.registration', 'a')
			->andWhere('a.tournament = ?1')
			->andWhere('a.summonerName = ?2')
			->setParameter(1, $tournament)
			->setParameter(2, $summonerName);
		return $query->getQuery()->getResult();
	}
}
