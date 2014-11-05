<?php

namespace Host\Controller;

class BaseControllerCore extends \Phalcon\Mvc\Controller
{
	public function debugToView()
	{
		$this->view->controller 	= get_class($this);
		$this->view->action 		= "index";
    	$this->view->path 			= $this->dispatcher->getParam('path');
	}
}