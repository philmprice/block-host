<?php

namespace Host\Controller;

class IndexControllerCore extends \Host\Controller\BaseControllerCore
{
    public function indexAction()
    {
		//	init
		$this->init();

		//	set main view
		$this->view->setMainView('block-host/index');
    }
}