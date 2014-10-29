<?php

////////////////////////////
//  BOOTSTRAP
    
require_once('../bootstrap.php');

////////////////////////////
//  RUN APPLICATION
    
try {
    
    $application = new \Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
    
     echo "PhalconException: ", $e->getMessage();
}