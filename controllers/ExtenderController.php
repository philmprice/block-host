<?php

class ExtenderController extends BaseController
{
	public function indexAction()
	{
		//	send debug vars to view
		$this->debugToView();

		//	debug test
		// $this->view->foo 	= Block\Module\Pages\Foo::Bar();

		//	extend requested folder
    	Extender::extend($this->dispatcher->getParam('path'));
	}
}