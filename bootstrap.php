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
//  DEFINITIONS
define('ABS_TO_HOST_PATH', '__core__\philmprice\block-host');
define('HOST_ROOT',         realpath(dirname(__FILE__)));
define('PROJ_HOST_ROOT',    str_replace('__core__', 'project', HOST_ROOT));
define('ABS_ROOT',          str_replace(ABS_TO_HOST_PATH,'',HOST_ROOT));


////////////////////////////
//  LOADER SETUP
$loaderDirArray = array(
    '../controllers/',
    '../models/',
    '../php/objects/'
);
$loaderNamespaceArray = array();

$loader = new \Phalcon\Loader();
$loader->registerDirs($loaderDirArray)->register();


////////////////////////////
//  VIEW SETUP
$viewDirArray = array();


////////////////////////////
//  DEPENDENCY INJECTOR
$di = new Phalcon\DI();
$di->set('dispatcher',  $dispatcher = new \Phalcon\Mvc\Dispatcher());
$di->set('response',    $response   = new \Phalcon\Http\Response());
$di->set('request',     $request    = new \Phalcon\Http\Request());
$di->set('router',      $router     = new \Phalcon\Mvc\Router());


////////////////////////////
//  ROOT
$di->set('url', function(){
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri('/');
    return $url;
});


////////////////////////////
//  ROUTER
$router->add("/extender", 
    array(
        'controller'    => 'extender',
        'action'        => 'index',
        'path'          => $_GET['path']
    )
);
$router->add("/extender/extend", 
    array(
        'controller'    => 'extender',
        'action'        => 'index',
        'path'          => $_GET['path']
    )
);


////////////////////////////
//  BOOTSTRAP other blocks
$blockFolderArray = AppBlock::getBlockFolderArray();
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

    //  create twig function 'inc' and register it
    $function   = new Twig_SimpleFunction('inc', function(Twig_Environment $env, $context, $template, $variables = array(), $withContext = true, $ignoreMissing = false, $sandboxed = false) {
        
        /*
        //  GET json filename
        $jsonFilename           = str_replace('.twig', '.json', $template);
        
        //  IF json default data exists
        if(file_exists(ABS_ROOT.'views/'.$jsonFilename))
        {
            //  GET data
            $arrData            = json_decode(file_get_contents(ABS_ROOT.'views/'.$jsonFilename), true);
            
            //  MERGE data into context
            foreach($arrData AS $key => $data)
            {
                if(!array_key_exists($key, $context))
                {
                    $context[$key]  = $data;
                }
            }
        }
        */

        //  GET paths for project verion of twig view
        $templateFilePath = HOST_ROOT.'/views/project/'.$template;
        $templateTwigPath = 'project/'.$template;

        // if project version of twig view exists
        if(file_exists($templateFilePath))
        {
            //  use it instead
            $template = $templateTwigPath;
        }

        //  INCLUDE and display template
        echo twig_include($env, $context, $template, $variables, $withContext, $ignoreMissing, $sandboxed);

    }, array('needs_environment' => true, 'needs_context' => true));

    //  register the function we just created
    $twig->getTwig()->addFunction($function);

    return $twig;

}, true);

$di->set('view', function (){

    //  create view
    $view = new \Phalcon\Mvc\View();

    //  set directories
    $view->setViewsDir('../views/');

    $view->registerEngines(array(
        '.twig' => 'twigService'

        /*
        ,".volt" => function($view, $di) {

            //  create volt
            $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
            
            // set  options
            $volt->setOptions(array(
              'compileAlways' => true  
            ));

            //  add filters
            $volt->getCompiler()->addFilter('p', function($resolvedArgs, $exprArgs) {

                return "project/sub-view.volt";
            });

            return $volt;
        }
        */
    ));

    return $view;
});
