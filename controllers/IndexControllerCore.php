<?php

namespace Host\Controller;

class IndexControllerCore extends \Host\Controller\BaseControllerCore
{
    public function indexAction()
    {
		//	send debug vars to view
		$this->debugToView();

		$this->view->foo = \Module\Pages\Foo::bar();
    }
}