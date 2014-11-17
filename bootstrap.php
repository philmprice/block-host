<?php

////////////////////////////
//  INCLUDES

require_once "../../../twig/twig/lib/Twig/Autoloader.php";  Twig_Autoloader::register();
require_once "php/objects/Twig.php";
require_once "php/objects/Twig/Environment.php";
require_once "php/objects/Twig/CoreExtension.php";
require_once "php/objects/Twig/Nodes/Assets.php";
require_once "php/objects/Twig/TokenParsers/Assets.php";
require_once 'php/functions/functions.php';

////////////////////////////
//  SERVER

define('SERVER', 'dev');
// define('SERVER', 'live');

////////////////////////////
//  DEFINITIONS

define('CORE_FOLDER',       'vendor');
define('PROJ_FOLDER',       'local');
define('ROOT',              getRootFromServerGlobal($_SERVER));
define('ABS_TO_HOST_PATH',  CORE_FOLDER.'/philmprice/block-host');
define('HOST_ROOT',         str_replace('\\', '/', realpath(dirname(__FILE__))));
define('PROJ_HOST_ROOT',    str_replace('\\', '/', str_replace(CORE_FOLDER, PROJ_FOLDER, HOST_ROOT)));
define('ABS_ROOT',          str_replace(ABS_TO_HOST_PATH,'',HOST_ROOT));
define('HOST_VENDOR',       'philmprice');
define('HOST_BLOCK',        'block-host');

////////////////////////////
//  LOADER SETUP

$blockAuthor    = 'philmprice';
$blockFolder    = 'block-host';
$loaderDirArray = array(
    '../models/'
);
$loaderNamespaceArray = array(
    'Host\Object'   => '../php/objects/',
    'Host\Model'    => '../models/'
);
$loaderClassArray = array(
    'Host\Controller\BaseControllerCore'       => ABS_ROOT.CORE_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/controllers/BaseControllerCore.php',
    'Host\Controller\BaseController'           => ABS_ROOT.PROJ_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/controllers/BaseController.php',
    
    'Host\Controller\IndexControllerCore'      => ABS_ROOT.CORE_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/controllers/IndexControllerCore.php',
    'Host\Controller\IndexController'          => ABS_ROOT.PROJ_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/controllers/IndexController.php',
    
    'Host\Controller\ExtenderControllerCore'   => ABS_ROOT.CORE_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/controllers/ExtenderControllerCore.php',
    'Host\Controller\ExtenderController'       => ABS_ROOT.PROJ_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/controllers/ExtenderController.php',
    
    'Model\NounCore'                           => ABS_ROOT.CORE_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/models/NounCore.php',
    'Model\Noun'                               => ABS_ROOT.PROJ_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/models/Noun.php',

    'Model\ModuleCore'                         => ABS_ROOT.CORE_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/models/ModuleCore.php',
    'Model\Module'                             => ABS_ROOT.PROJ_FOLDER.'/'.$blockAuthor.'/'.$blockFolder.'/models/Module.php'

);

$loader = new \Phalcon\Loader();
$loader->registerDirs($loaderDirArray)->registerNamespaces($loaderNamespaceArray)->registerClasses($loaderClassArray)->register();

////////////////////////////
//  DEPENDENCY INJECTOR

$di = new Phalcon\DI();
$di->set('dispatcher',      $dispatcher     = new \Phalcon\Mvc\Dispatcher());
$di->set('response',        $response       = new \Phalcon\Http\Response());
$di->set('request',         $request        = new \Phalcon\Http\Request());
$di->set('router',          $router         = new \Phalcon\Mvc\Router());
$di->set('assets',          $assets         = new \Phalcon\Assets\Manager());
$di->set('escaper',         $escaper        = new \Phalcon\Escaper());
$di->set('filter',          $filter         = new \Phalcon\Filter());
$di->set('modelsManager',   $modelsManager  = new \Phalcon\Mvc\Model\Manager());
$di->set('modelsMetadata',  $modelsMetadata = new \Phalcon\Mvc\Model\Metadata\Memory());
$di->set('db',              $db             = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                                                "host"      => "localhost",
                                                "username"  => "root",
                                                "password"  => "",
                                                "dbname"    => "blocks-dev"
                                            )));

////////////////////////////
//  BOOTSTRAP other blocks

$blockFolderArray = Host\Object\AppBlockCore::getBlockFolderArray();
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
//  BIND LOADER

$loader = new \Phalcon\Loader();
$loader->registerDirs(      $loaderDirArray);
$loader->registerClasses(   $loaderClassArray);
$loader->registerNamespaces($loaderNamespaceArray);
$loader->register();

////////////////////////////
//  SYNC PROJECT FOLDER

if(SERVER == 'dev' && isset($_GET['refreshAll']))
{
    Host\Object\ExtenderCore::SyncProjectFolder();
}

////////////////////////////
//  VIEW SETUP

$viewDirArray = array();

////////////////////////////
//  ROOT

$di->set('url', function(){
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri('/');
    return $url;
});

////////////////////////////
//  ROUTER
$router->setDefaults( 
    array(
        'controller'    => 'index',             //  what controller class do i go to?
        'action'        => 'index',             //  what function do i run in that class?
        'namespace'     => 'Host\Controller'    //  what namespace is the controller class in?
    )
);
$router->add("/extender", 
    array(
        'controller'    => 'extender',
        'action'        => 'index',
        'path'          => $_GET['path'],
        'namespace'     => 'Host\Controller'
    )
);
$router->add("/extender/extend", 
    array(
        'controller'    => 'extender',
        'action'        => 'index',
        'path'          => $_GET['path'],
        'namespace'     => 'Host\Controller'
    )
);

////////////////////////////
//  BIND VIEW

Twig_Autoloader::register();

$di['twigService'] = function($view, $di) {
    Phalcon\Mvc\View\Engine\Twig::setOptions(array(
        'debug'               => true,
        'charset'             => 'UTF-8',
        'base_template_class' => 'Twig_Template',
        'strict_variables'    => false,
        'autoescape'          => false,
        'cache'               => __DIR__.'/../../cache/twig/',
        'auto_reload'         => null,
        'optimizations'       => -1,
    ));
 
    $twig = new View($view, $di);
    return $twig;
};

$di->set('twigService', function($view, $di) use ($config) {

    //  create twig object
    $option     = array('cache' => '../cache/');
    $arrFolder  = array('../views/');
    $twig       = new \Phalcon\Mvc\View\Engine\Twig($view, $di, $options, $arrFolder);

    return $twig;

}, true);

$di->set('view', function (){

    //  create view
    $view = new \Phalcon\Mvc\View();

    //  set directories
    $view->setViewsDir('../views/');

    $view->registerEngines(array(
        '.twig' => 'twigService'
    ));

    return $view;
});