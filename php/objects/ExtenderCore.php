<?php

namespace Host\Object;

class ExtenderCore
{
    static $platformPath  = '../../../../../../';

    public static function syncProjectFolder()
    {
        //  init
        $blockFolderArray   = array();

        //  get vendor folder array
        $vendorFolderArray  = FileCore::getFoldersAt(ABS_ROOT.CORE_FOLDER);

        //  process vendor folders
        foreach($vendorFolderArray AS $vendorFolder)
        {
            //  get vendor sub folder array
            $vendorSubFolderArray   = FileCore::getFoldersAt(ABS_ROOT.CORE_FOLDER.'/'.$vendorFolder);

            //  process vendor sub folder array
            foreach($vendorSubFolderArray AS $vendorSubFolder)
            {
                //  if vendor sub folder is a block
                if(strpos($vendorSubFolder, 'block-') === 0)
                {
                    $blockFolderArray[] = array('path'      => $vendorFolder.'\\'.$vendorSubFolder,
                                                'vendor'    => $vendorFolder,
                                                'block'     => $vendorSubFolder);
                }
            }
        }

        // process block folder array
        foreach($blockFolderArray AS $blockFolderInfo)
        {
            //  extend this block folder
            self::extend($blockFolderInfo['path']);

            //  if the link needs created
            if(!is_link('..\\views\\'.$blockFolderInfo['block']))
            {
                //  ensure view symlink folders
                \Host\Object\FileCore::ensurePath("../views/core");

                //  ensure js symlink folders
                \Host\Object\FileCore::ensurePath("../www/js");
                \Host\Object\FileCore::ensurePath("../www/js/core");

                //  ensure css symlink folders
                \Host\Object\FileCore::ensurePath("../www/css");
                \Host\Object\FileCore::ensurePath("../www/css/core");

                //  ensure fonts symlink folders
                \Host\Object\FileCore::ensurePath("../www/fonts");
                \Host\Object\FileCore::ensurePath("../www/fonts/core");

                //  if windows
                if(thisIsWindows())
                {
                    //  project views link
                    $makeProjectLink    = 'mklink /j "..\\views\\'              .$blockFolderInfo['block'].'" "..\\..\\..\\..\\'.PROJ_FOLDER.'\\'.$blockFolderInfo['path'].'\\views\\"';
                    shell_exec($makeProjectLink);

                    //  core views link
                    $makeCoreLink       = 'mklink /j "..\\views\\core\\'        .$blockFolderInfo['block'].'" "..\\..\\..\\..\\'.CORE_FOLDER.'\\'.$blockFolderInfo['path'].'\\views\\"';
                    shell_exec($makeCoreLink);

                    //  project css link
                    $makeProjectLink    = 'mklink /j "..\\www\\css\\'           .$blockFolderInfo['block'].'" "..\\..\\..\\..\\'.PROJ_FOLDER.'\\'.$blockFolderInfo['path'].'\\css\\"';
                    shell_exec($makeProjectLink);

                    //  core css link
                    $makeCoreLink       = 'mklink /j "..\\www\\css\\core\\'     .$blockFolderInfo['block'].'" "..\\..\\..\\..\\'.CORE_FOLDER.'\\'.$blockFolderInfo['path'].'\\css\\"';
                    shell_exec($makeCoreLink);

                    //  project fonts link
                    $makeProjectLink    = 'mklink /j "..\\www\\fonts\\'         .$blockFolderInfo['block'].'" "..\\..\\..\\..\\'.PROJ_FOLDER.'\\'.$blockFolderInfo['path'].'\\fonts\\"';
                    shell_exec($makeProjectLink);

                    //  core fonts link
                    $makeCoreLink       = 'mklink /j "..\\www\\fonts\\core\\'   .$blockFolderInfo['block'].'" "..\\..\\..\\..\\'.CORE_FOLDER.'\\'.$blockFolderInfo['path'].'\\fonts\\"';
                    shell_exec($makeCoreLink);

                    //  project js link
                    $makeProjectLink    = 'mklink /j "..\\www\\js\\'            .$blockFolderInfo['block'].'" "..\\..\\..\\..\\'.PROJ_FOLDER.'\\'.$blockFolderInfo['path'].'\\js\\"';
                    shell_exec($makeProjectLink);

                    //  core js link
                    $makeCoreLink       = 'mklink /j "..\\www\\js\\core\\'      .$blockFolderInfo['block'].'" "..\\..\\..\\..\\'.CORE_FOLDER.'\\'.$blockFolderInfo['path'].'\\js\\"';
                    shell_exec($makeCoreLink);
                }
                //  if linux
                else
                {
                    //  project views link
                    $makeProjectLink    = '';
                    shell_exec($makeProjectLink);

                    //  core views link
                    $makeCoreLink       = '';
                    shell_exec($makeCoreLink);
                }
            }
        }
    }

    public static function extend($vendorPath)
    {
        //  core root
        $coreRoot   = ABS_ROOT.CORE_FOLDER.'/';

        //  if folder exists
        if(is_dir($coreRoot.$vendorPath))
        {
            //  GET subfolders you need to hold extended files
            $absFolderArray     = FileCore::getAllFolderPathsAt($coreRoot.$vendorPath, array(
                'block-host/views/block-host',
                'block-host/views/core'
            ));

            //  ADD this folder to array
            $absFolderArray[]   = $coreRoot.$vendorPath;

            //  THEN extend files in the subfolders
            foreach($absFolderArray AS $absFolder)
            {
                self::extendFilesInFolder($absFolder);
            }
        }
    }

    public static function extendFilesInFolder($parentFolder)
    {
        //  GET files in folder
        $fileArray    = FileCore::getFilesInFolder($parentFolder);

        //  for each file
        foreach($fileArray AS $file)
        {
            //  extend file
            self::extendFolderFile($parentFolder, $file);
        }
    }

    public static function extendFolderFile($folderPath, $file)
    {
        //  GET extension
        $extension  = pathinfo($folderPath.$file, PATHINFO_EXTENSION);

        try
        {
            //  IF model file
            if((strpos($folderPath, '/models/') !== false) && ($extension == 'php') && $file == ucfirst($file))
            {
                self::extendObjectFile($folderPath, $file);
            }
            //  IF twig view file
            elseif((strpos($folderPath, '/views/') !== false) && ($extension == 'twig'))
            {
                self::extendTwigViewFile($folderPath, $file);
            }
            //  IF controller file
            elseif((strpos($folderPath, '/controllers/') !== false) && ($extension == 'php') && $file == ucfirst($file))
            {
                self::extendObjectFile($folderPath, $file);
            }
            //  IF object file
            elseif((strpos($folderPath, '/objects/') !== false) && ($extension == 'php') && $file == ucfirst($file))
            {
                self::extendObjectFile($folderPath, $file);
            }
        }
        catch(Exception $e)
        {
            debug($e->getMessage());
            exit;
        }
    }

    public static function extendTwigViewFile($corePath, $file)
    {
        //  if file exists
        if(file_exists($corePath.$file))
        {
            //  set template vars
            $corePath           = str_replace('\\', '/', $corePath);
            $blockVendor        = explode('/',str_replace(ABS_ROOT.CORE_FOLDER.'/',                  '', $corePath))[0];
            $blockFolder        = explode('/',str_replace(ABS_ROOT.CORE_FOLDER.'/'.$blockVendor.'/', '', $corePath))[0];
            $projectPath        = str_replace(CORE_FOLDER, PROJ_FOLDER, $corePath);
            $projectFile        = str_replace('.core', '', $file);

            //  if project file doesn't exist, create it
            if(!file_exists($projectPath.$projectFile))
            {
                FileCore::ensurePath($projectPath);
                //  start buffer
                ob_start();

                //  run php template
                require(HOST_ROOT.'/php/objects/Extender/twig.template.php');

                //  get content and end buffer
                $extendedFileContent = ob_get_clean();

                //  write destination 
                file_put_contents($projectPath.$projectFile, $extendedFileContent);
            }
        }
        else
        {
            throw new Exception('Caught attempt to extend a non-existant file: '.$corePath.$file);
        }
    }

    public static function extendObjectFile($corePath, $file)
    {
        //  if file exists
        if(file_exists($corePath.$file))
        {
            //  ensure destination path
            $projectPath        = str_replace(CORE_FOLDER, PROJ_FOLDER, $corePath);
            FileCore::ensurePath($projectPath);

            //  set template vars
            $namespace          = FileCore::getNamespaceFromFile($corePath.$file);
            $class              = str_replace('Core.php', '', $file);
            $projectFile        = str_replace('Core', '', $file);
            $coreClass          = str_replace('.php', '', $file);
            $classPath          = $namespace.'\\'.$coreClass;
            $methodPhpArray     = array();

            //  if project file doesn't exist, create it
            if(!file_exists($projectPath.$projectFile))
            {
                try
                {
                    if(class_exists($classPath, true))
                    {
                        $reflection = new \ReflectionClass($classPath);
                    }
                    else
                    {
                        debug('ExtenderCore.php says: Unable to load class, so cannot extend: '.$classPath);
                    }
                }
                catch(Exception $e)
                {
                    // outcomeds handled below
                    debug('ExtenderCore.php says: Could not create class '.$classPath);
                }

                // debug('namespace: '.$namespace);

                if(is_object($reflection))
                {
                    //  get methods
                    $methods['all']         = $reflection->getMethods();
                    $methods['abstract']    = $reflection->getMethods(\ReflectionMethod::IS_ABSTRACT);
                    $methods['final']       = $reflection->getMethods(\ReflectionMethod::IS_FINAL);
                    $methods['public']      = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                    $methods['private']     = $reflection->getMethods(\ReflectionMethod::IS_PRIVATE);
                    $methods['protected']   = $reflection->getMethods(\ReflectionMethod::IS_PROTECTED);
                    $methods['static']      = $reflection->getMethods(\ReflectionMethod::IS_STATIC);

                    foreach($methods['all'] AS $method)
                    {
                        //  build method type string
                        $methodTypeString   = '';
                        $methodTypeString  .= in_array($method, $methods['abstract'])    ? 'abstract '   : '';
                        $methodTypeString  .= in_array($method, $methods['final'])       ? 'final '      : '';
                        $methodTypeString  .= in_array($method, $methods['public'])      ? 'public '     : '';
                        $methodTypeString  .= in_array($method, $methods['private'])     ? 'private '    : '';
                        $methodTypeString  .= in_array($method, $methods['protected'])   ? 'protected '  : '';
                        $methodTypeString  .= in_array($method, $methods['static'])      ? 'static '     : '';

                        //  store method info
                        $methodInfoArray[]  = array(
                            'name'          => $method->name,
                            'declaration'   => $methodTypeString.'function '.$method->name
                        );
                    }

                    //  start buffer
                    ob_start();

                    //  run php template
                    require(HOST_ROOT.'/php/objects/Extender/object.template.php');

                    //  get content and end buffer
                    $extendedFileContent = ob_get_clean();

                    //  write destination 
                    file_put_contents($projectPath.$projectFile, $extendedFileContent);
                }
            }
        }
        else
        {
            throw new \Exception('Caught attempt to extend a non-existant file: '.$corePath.$file);
        }
    }

    public static function projectFileExists($vendorPath)
    {
        //  BUILD absolute path
        $absPath    = ABS_ROOT.CORE_FOLDER.'/'.$vendorPath;

        return file_exists($absPath);
    }
}