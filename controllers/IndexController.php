<?php

class IndexController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {
        $this->view->bodyContent = 'hop on a pancake '.\Module\Pages\Foo::bar();
    }
    
    public function four04Action()
    {
        $this->view->bodyContent = "<h2>404</h2>";
    }
}