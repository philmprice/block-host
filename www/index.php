<?

////////////////////////////
//  LOADER

$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        '../app/controllers/',
        '../app/models/'
    )
)->register();

////////////////////////////
//  DEPENDENCY INJECTOR

$di = new Phalcon\DI();
$di->set('dispatcher',  $dispatcher = new \Phalcon\Mvc\Dispatcher());
$di->set('response',    $response   = new \Phalcon\Http\Response());
$di->set('request',     $request    = new \Phalcon\Http\Request());
$di->set('router',      $router     = new \Phalcon\Mvc\Router());

//  views
$di->set('view', function(){
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir('../app/views/');
    return $view;
});

//  base uri
$di->set('url', function(){
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri('/');
    return $url;
});

////////////////////////////
//  ROUTER

$router->add(
    "/second", 
    array(
        'controller'    => 'index',
        'action'        => 'second',
    )
);

try {
    
    ////////////////////////////
    //  APPLICATION
    
    $application = new \Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
    
     echo "PhalconException: ", $e->getMessage();
}