<?

////////////////////////////
//  LOADER directories
$loaderDirArray = array(
                    '../controllers/',
                    '../models/',
                    '../php/objects/'
                );

$loader = new \Phalcon\Loader();

$loader->registerDirs($loaderDirArray)->register();

////////////////////////////
//  VIEW directories
$viewDirArray = array();

////////////////////////////
//  DEPENDENCY INJECTOR
$di = new Phalcon\DI();
$di->set('dispatcher',  $dispatcher = new \Phalcon\Mvc\Dispatcher());
$di->set('response',    $response   = new \Phalcon\Http\Response());
$di->set('request',     $request    = new \Phalcon\Http\Request());
$di->set('router',      $router     = new \Phalcon\Mvc\Router());

////////////////////////////
//  START VIEW 


////////////////////////////
//  ROOT
$di->set('url', function(){
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri('/');
    return $url;
});

////////////////////////////
//  ROUTER
$router->add(
    "/404", 
    array(
        'controller'    => 'index',
        'action'        => 'four04',
    )
);

////////////////////////////
//  BOOTSTRAP other blocks
$blockFolderArray = Block::getBlockFolderArray();
foreach($blockFolderArray AS $blockFolder)
{
    //  IF block folder's bootstrap file exists
    if(file_exists($blockFolder.'bootstrap.php'))
    {
        //  RUN boostrap for block
        require_once($blockFolder.'bootstrap.php');
    }
}

////////////////////////////
//  COMMIT LOADER
$loader = new \Phalcon\Loader();
$loader->registerDirs($loaderDirArray)->register();

////////////////////////////
//  COMMIT VIEW

$di->set('view', function (){
    $view = new \Phalcon\Mvc\View();

    $view->setViewsDir('../views/');
    
    global $viewDirArray;
    foreach($viewDirArray AS $viewDir)
    {
        $view->setPartialsDir($viewDir);
    }

    $view->registerEngines(array(
        ".volt" => 'Phalcon\Mvc\View\Engine\Volt'
    ));
    
    return $view;
});

try {
    
    ////////////////////////////
    //  APPLICATION
    
    $application = new \Phalcon\Mvc\Application($di);
    echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {
    
     echo "PhalconException: ", $e->getMessage();
}