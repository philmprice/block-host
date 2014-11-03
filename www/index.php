<?php

////////////////////////////
//  BOOTSTRAP
    
require_once('../bootstrap.php');

// debug(ABS_TO_HOST_PATH);
// debug(HOST_ROOT);
// debug(ABS_ROOT);

// Extender::extend('philmprice/block-host/views/');

////////////////////////////
//  RUN APPLICATION
    
try {
    
    $application = new \Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
    
     echo "PhalconException: ", $e->getMessage();
}