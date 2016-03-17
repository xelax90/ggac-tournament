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
use GGACTournament\Tournament\RoundCreator\Manager as RoundCreatorManager;
use Zend\Form\FormInterface;
use GGACTournament\Tournament\RoundCreator\RoundConfig;
use GGACTournament\Tournament\ProviderAwareInterface;
use GGACTournament\Tournament\ProviderAwareTrait;

/**
 * Description of AdminRoundController
 *
 * @author schurix
 */
class AdminRoundController extends ListController implements ProviderAwareInterface{
	use ProviderAwareTrait;
	
	/** @var RoundCreatorManager */
	protected $roundCreatorManager;
	
	/**
	 * @return RoundCreatorManager
	 */
	protected function getRoundCreatorManager() {
		return $this->roundCreatorManager;
	}
	
	/**
	 * @param RoundCreatorManager $roundCreatorManager
	 * @return AdminRoundController
	 */
	public function setRoundCreatorManager(RoundCreatorManager $roundCreatorManager) {
		$this->roundCreatorManager = $roundCreatorManager;
		return $this;
	}
	
	protected function getCreateForm() {
		return $this->getRoundCreatorManager()->getCreateForm();
	}
	
	protected function getEditForm() {
		return $this->getRoundCreatorManager()->getEditForm();
	}
	
	protected function _createItem($item, $form, $data = null) {
		/* @var $item \GGACTournament\Entity\Round */
		$em = $this->getEntityManager();
        $request = $this->getRequest();
		if($data === null){
			$data = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
		}
		/* @var $form \GGACTournament\Form\RoundForm */
        $form->bind($item);
        $form->setData($data);
        if ($form->isValid()) {
			if(!empty($this->getParentControllerOptions())){
				$parentId = $this->getEvent()->getRouteMatch()->getParam($this->getParentControllerOptions()->getIdParamName());
				$setter = $this->createSetter($this->getOptions()->getParentAttributeName());
				if(method_exists($item, $setter)){
					$parent = $this->getItem($parentId, $this->getParentControllerOptions());
					call_user_func(array($item, $setter), $parent);
				}
			}
			
			$formData = $form->getData(FormInterface::VALUES_AS_ARRAY);
			$formData['round']['startDate'] = $item->getStartDate();
			$roundConfig = new RoundConfig($formData['round']);
			
			$gameCheck = null;
			switch($formData['gameCheck']){
				case 'currentPhase' : 
					$gameCheck = $item->getGroup()->getPhase();
					break;
				case 'currentGroup' :
					$gameCheck = $item->getGroup();
					break;
				case 'previousRound' :
					$gameCheck = $item->getGroup()->getLastRound();
					break;
				case 'tournament':
				default :
					$gameCheck = $this->getTournamentProvider()->getTournament();
			}
			
			$this->getRoundCreatorManager()->nextRound($formData['round']['roundType'], $item->getGroup(), $roundConfig, $gameCheck);
			return true;
        }
		return false;
	}
}
