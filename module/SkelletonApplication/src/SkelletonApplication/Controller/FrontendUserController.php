<?php

/*
 * Copyright (C) 2015 schurix
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

namespace SkelletonApplication\Controller;

use Zend\View\Model\ViewModel;
use ZfcUser\Controller\UserController;
use Doctrine\ORM\EntityManager;
use SkelletonApplication\Entity\User;
use SkelletonApplication\Service\UserService;
use SkelletonApplication\Options\SkelletonOptions;

/**
 * Description of FrontendUserController
 *
 * @author schurix
 */
class FrontendUserController extends UserController{
	
	/** @var EntityManager */
	protected $em;
	
	/** @var UserService */
	protected $skelletonUserService;
	
	/**
	 * @param EntityManager $em
	 */
	public function setEntityManager(EntityManager $em){
		$this->em = $em;
	}
	
	/**
	 * @return EntityManager
	 */
	public function getEntityManager(){
		if (null === $this->em) {
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}
		return $this->em;
	}
	
	/**
	 * @return UserService
	 */
	public function getSkelletonUserService(){
		if (null === $this->skelletonUserService) {
			$this->skelletonUserService = $this->getServiceLocator()->get('SkelletonApplication\UserService');
		}
		return $this->skelletonUserService;
	}
	
	public function checkTokenAction(){
		$model = new ViewModel();
		$model->setTemplate('zfc-user/user/checktoken');
		
		$userService = $this->getSkelletonUserService();
		
		// remove old, not verified records 
		$userService->cleanExpiredVerificationRequests();
		
		// Pull and validate the Request Key
		$token = $this->getEvent()->getRouteMatch()->getParam('token', false);
		if ( !$token ) {
			$model->setVariables(array('success' => false, 'message' => gettext_noop('Invalid Token!')));
			return $model;
		}
		
		$validator = new \Zend\Validator\Hex();
		if ( !$validator->isValid($token) ) {
			$model->setVariables(array('success' => false, 'message' => gettext_noop('Invalid Token!')));
			return $model;
		}
		
		// Find the token in DB
		$user = $userService->findByToken($token);
		if ( ! $user instanceof User ) {
			$model->setVariables(array('success' => false, 'message' => gettext_noop('Invalid Token!')));
			return $model;
		}
		
		
		/* @var $options \SkelletonApplication\Options\SkelletonOptions */
		$options = $this->getServiceLocator()->get('SkelletionApplication\Options\Application');
		$flag = $options->getRegistrationMethodFlag();
		
		$user->setEmailIsVerified(true);
		$variables = array('success' => true, 'activated' => false);
		if(!($flag & SkelletonOptions::REGISTRATION_METHOD_MODERATOR_CONFIRM) && !($flag & SkelletonOptions::REGISTRATION_METHOD_AUTO_ENABLE)){
			$user->setIsActive(true);
			$variables['activated'] = true;
		}
		$this->getEntityManager()->flush();
		$this->sendEmailVerified($user);
		
		$model->setVariables($variables);
		return $model;
	}
	
	protected function sendEmailVerified($user){
		/* @var $options SkelletonOptions */
		$options = $this->getServiceLocator()->get('SkelletionApplication\Options\Application');
		
		$flag = $options->getRegistrationMethodFlag();
		
		if($flag & SkelletonOptions::REGISTRATION_METHOD_MODERATOR_CONFIRM){
			/* @var $transport \GoalioMailService\Mail\Service\Message */
			$transport = $this->getServiceLocator()->get('goaliomailservice_message');
			
			if($options->getRegistrationEmailFlag() & SkelletonOptions::REGISTRATION_EMAIL_DOUBLE_CONFIRM_MAIL){
				$email = $options->getRegistrationUserEmailDoubleConfirm();
				$message = $transport->createHtmlMessage($options->getRegistrationNotificationFrom(), $user->getEmail(), $email->getSubject(), $email->getTemplate(), array('user' => $user));
				$transport->send($message);
			}
			
			if($options->getRegistrationEmailFlag() & SkelletonOptions::REGISTRATION_EMAIL_MODERATOR){
				$users = $this->getEntityManager()->getRepository(get_class($user))->createQueryBuilder('u')
						->leftJoin('u.roles', 'r')
						->andWhere('r.roleId IN (:roleIds)')
						->setParameter('roleIds', $options->getRegistrationNotify());
				$email = $options->getRegistrationModeratorEmail();
				foreach($users as $mod){
					$message = $transport->createHtmlMessage($options->getRegistrationNotificationFrom(), $mod->getEmail(), $email->getSubject(), $email->getTemplate(), array('user' => $user, 'moderator' => $mod));
					$transport->send($message);
				}
			}
		}
	}
}