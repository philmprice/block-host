<?php

namespace Host\Controller;

class ExtenderControllerCore extends \Host\Controller\BaseControllerCore
{
	public function indexAction()
	{
		//	send debug vars to view
		$this->debugToView();

		//	extend requested folder
    	Extender::extend($this->dispatcher->getParam('path'));
	}
}