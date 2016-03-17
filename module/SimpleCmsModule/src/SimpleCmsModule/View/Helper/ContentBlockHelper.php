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

namespace SimpleCmsModule\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Description of ContentBlockHelper
 *
 * @author schurix
 */
class ContentBlockHelper extends AbstractHelper {
	
	/** @var \SimpleCmsModule\Service\Block */
	protected $blockService;
	
	protected function getBlockService() {
		return $this->blockService;
	}

	public function setBlockService($blockService) {
		$this->blockService = $blockService;
		return $this;
	}

	public function __invoke(){
		return $this;
	}
	
	/**
	 * Renders all content blocks for passed position. By default it will use 'partial/content_blocks' view script
	 * @param string $position
	 * @param string $partial
	 * @return string
	 */
	public function renderPosition($position, $partial = null, $params = array()){
		if($partial === null){
			$partial = 'partial/content_blocks';
		}
		$blocks = $this->getBlockService()->getBlocks($position);
		return $this->getView()->render($partial, array('blocks' => $blocks, 'params' => $params));
	}
}
