<?php

namespace Host\Controller;

class ExtenderControllerCore extends \Host\Controller\BaseControllerCore
{
	public function indexAction()
	{
		//	init
		$this->init();

		\Host\Object\ExtenderCore::SyncProjectFolder();
	}
}