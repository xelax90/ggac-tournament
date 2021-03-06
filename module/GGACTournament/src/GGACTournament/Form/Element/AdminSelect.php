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

use DoctrineModule\Form\Element\ObjectSelect;
use SkelletonApplication\Entity\User;

/**
 * Description of TeamSelect
 *
 * @author schurix
 */
class AdminSelect extends ObjectSelect{
	public function setDefaultOptions(){
		/* @var $userRepo \Doctrine\ORM\EntityRepository */
		$userRepo = $this->proxy->getObjectManager()->getRepository(User::class);
		$users = $userRepo->createQueryBuilder('u')
				->leftJoin('u.roles', 'r')
				->andWhere('r.roleId IN (:adminRoles)')
				->setParameter('adminRoles', array('moderator', 'administrator'))
				->getQuery()
				->getResult();
		// executes query by hand to avoid creating a new repository
		$ids = array();
		foreach($users as $user){
			$ids[] = $user->getId();
		}
		$defaultOptions = array(
			'target_class'   => User::class,
			'label_generator' => function($user) {
				return $user->getDisplayName();
			},
			'find_method' => array(
				'name'   => 'findBy',
				'params' => array(
					'criteria' => array(
						'id' => $ids,
					),
				),
			),
		);
		return $this->setOptions($defaultOptions);
	}
}
