<?php

namespace Host\Controller;

class IndexControllerCore extends \Host\Controller\BaseControllerCore
{
    public function indexAction()
    {
		//	send debug vars to view
		$this->debugToView();

		//	set main view
		$this->view->setMainView('block-host/index');

		//	set view variables
		$this->view->foo = \Module\Pages\Foo::bar();
    }
}