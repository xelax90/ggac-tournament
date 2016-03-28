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

namespace GGACTournament\Controller;

use XelaxAdmin\Controller\ListController;
use GGACTournament\Tournament\ProviderAwareInterface;
use GGACTournament\Tournament\ProviderAwareTrait;
use GGACTournament\Entity\Group;
use GGACTournament\Form\TeamSelectForm;
use GGACTournament\Entity\GroupTeamMapping;

/**
 * Description of AdminGroupTeamController
 *
 * @author schurix
 */
class AdminGroupTeamMappingController extends ListController implements ProviderAwareInterface{
	use ProviderAwareTrait;
	
	protected function getAll() {
		$em = $this->getEntityManager();
		$parentId = $this->getEvent()->getRouteMatch()->getParam($this->getParentControllerOptions()->getIdParamName());
		$items = $em->getRepository(GroupTeamMapping::class)->findBy(array($this->getOptions()->getParentAttributeName() => $parentId, ), array('seed' => 'ASC'));
		return $items;
	}
	
	protected function getItem($id = null, $option = null) {
		if(empty($id) || ($option !== null && $this->getOptions() != $option)){
			return parent::getItem($id, $option);
		}
		$groupId = $this->getEvent()->getRouteMatch()->getParam($this->getParentControllerOptions()->getIdParamName());
		return $this->getEntityManager()->getRepository(GroupTeamMapping::class)->find(array('group' => (int) $groupId, 'team' => (int) $id));
	}
	
	protected function getEditForm() {
		$form = parent::getEditForm();
		$form->setShowTeamSelect(false);
		return $form;
	}
	
	protected function _showEditForm($params) {
		$item = $this->getItem($params['id']);
		$title = $item->getTeam()->getName();
		$params['title'] .= ' - '.$title;
		return parent::_showEditForm($params);
	}
}
