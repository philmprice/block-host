<?php

namespace Host;

class ExtenderCore
{
    static $platformPath  = '../../../../../../';

    public static function syncProjectFolder()
    {
        //  init
        $blockFolderArray   = array();

        //  get vendor folder array
        $vendorFolderArray  = FileCore::getFoldersAt(ABS_ROOT.'__core__');

        //  process vendor folders
        foreach($vendorFolderArray AS $vendorFolder)
        {
            //  get vendor sub folder array
            $vendorSubFolderArray   = FileCore::getFoldersAt(ABS_ROOT.'__core__/'.$vendorFolder);

            //  process vendor sub folder array
            foreach($vendorSubFolderArray AS $vendorSubFolder)
            {
                //  if vendor sub folder is a block
                if(strpos($vendorSubFolder, 'block-') === 0)
                {
                    $blockFolderArray[] = $vendorFolder.'/'.$vendorSubFolder;
                }
            }
        }

        // process block folder array
        foreach($blockFolderArray AS $blockFolder)
        {
            //  extend this block folder
            self::extend($blockFolder);

            //  ensure symbolic links are in place
                //  views
                //  controllers
                //  objects
                //  models
        }
    }

    public static function extend($vendorPath)
    {
        //  core root
        $coreRoot   = ABS_ROOT.'__core__/';

        //  if folder exists
        if(is_dir($coreRoot.$vendorPath))
        {
            //  GET subfolders you need to hold extended files
            $absFolderArray     = FileCore::getAllFolderPathsAt($coreRoot.$vendorPath);

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

        foreach($fileArray AS $file)
        {
            //  GET extension
            $extension  = pathinfo($parentFolder.$file, PATHINFO_EXTENSION);

            try
            {
                //  IF model file
                if((strpos($parentFolder, '/models/') !== false) && ($extension == 'php') && $file == ucfirst($file))
                {
                    self::extendModelFile($parentFolder, $file);
                }
                //  IF twig view file
                elseif((strpos($parentFolder, '/views/') !== false) && ($extension == 'twig') && $file == ucfirst($file))
                {
                    self::extendTwigViewFile($parentFolder, $file);
                }
                //  IF controller file
                elseif((strpos($parentFolder, '/controllers/') !== false) && ($extension == 'php') && $file == ucfirst($file))
                {
                    self::extendObjectFile($parentFolder, $file);
                }
                //  IF object file
                elseif((strpos($parentFolder, '/objects/') !== false) && ($extension == 'php') && $file == ucfirst($file))
                {
                    self::extendObjectFile($parentFolder, $file);
                }
            }
            catch(Exception $e)
            {
                debug($e->getMessage());
                exit;
            }
        }
    }

    public static function extendModelFile($corePath, $file)
    {
        /*
        //  if file exists
        if(file_exists($corePath.$file))
        {
            $fileContent    = file_get_contents($corePath.$file);
            $projectPath    = str_replace('__core__', 'project', $corePath);

            File::ensurePath($projectPath);
            file_put_contents($projectPath.$file, $fileContent);
        }
        else
        {
            throw new Exception('Caught attempt to extend a non-existant file: '.$corePath.$file);
        }
        */
    }

    public static function extendTwigViewFile($corePath, $file)
    {
        //  if file exists
        if(file_exists($corePath.$file))
        {
            //  ensure destination path
            $projectPath        = str_replace('__core__', 'project', $corePath);
            $vendorRoot         = ABS_ROOT.'project/'.HOST_VENDOR.'/';
            $blockName          = str_replace(array($vendorRoot, 'views/'), '', $projectPath);
            FileCore::ensurePath($projectPath);

            //  set template vars
            $projectFile        = str_replace('.core', '', $file);
            $coreTwig           = $blockName.'core/'.$projectFile;

            //  start buffer
            ob_start();

            //  run php template
            require(HOST_ROOT.'/php/objects/Extender/twig.template.php');

            //  get content and end buffer
            $extendedFileContent = ob_get_clean();

            //  write destination 
            file_put_contents($projectPath.$projectFile, $extendedFileContent);
        }
        else
        {
            throw new Exception('Caught attempt to extend a non-existant file: '.$corePath.$file);
        }
    }

    public static function extendControllerFile($corePath, $file)
    {
        /*
        //  if file exists
        if(file_exists($corePath.$file))
        {
            //  get source content
            $fileContent    = file_get_contents($corePath.$file);

            //  destination project path to write to
            $projectPath    = str_replace('__core__', 'project', $corePath);

            //  write extended file content
            file_put_contents($projectPath.$file, $fileContent);
        }
        else
        {
            throw new Exception('Caught attempt to extend a non-existant file: '.$corePath.$file);
        }
        */
    }

    public static function extendObjectFile($corePath, $file)
    {
        //  if file exists
        if(file_exists($corePath.$file))
        {
            //  ensure destination path
            $projectPath        = str_replace('__core__', 'project', $corePath);
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
                        debug('class does not exist: '.$classPath);
                    }
                }
                catch(Exception $e)
                {
                    // outcomeds handled below
                    debug('could not create class '.$classPath);
                }

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

                    debug($methodInfoArray);

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
            throw new Exception('Caught attempt to extend a non-existant file: '.$corePath.$file);
        }
    }

    public static function projectFileExists($vendorPath)
    {
        //  BUILD absolute path
        $absPath    = ABS_ROOT.'__core__/'.$vendorPath;

        return file_exists($absPath);
    }
}