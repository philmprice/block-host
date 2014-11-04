<?php

class ExtenderController extends BaseController
{
	public function indexAction()
	{
		//	send debug vars to view
		$this->debugToView();

		//	extend requested folder
    	Extender::extend($this->dispatcher->getParam('path'));
	}
}