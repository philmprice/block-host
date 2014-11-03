<?php

class Extender
{
    static $platformPath  = '../../../../../../';

    public static function extend($vendorPath)
    {
        //  core root
        $coreRoot   = ABS_ROOT.'__core__/';

        //  if folder exists
        if(is_dir($coreRoot.$vendorPath))
        {
            //  GET subfolders you need to hold extended files
            $absFolderArray     = File::getAllFolderPathsAt($coreRoot.$vendorPath);

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
        $fileArray    = File::getFilesInFolder($parentFolder);

        foreach($fileArray AS $file)
        {
            //  GET extension
            $extension  = pathinfo($parentFolder.$file, PATHINFO_EXTENSION);

            try
            {
                //  IF model file
                if((strpos($parentFolder, '/models/') !== false) && ($extension == 'php'))
                {
                    self::extendModelFile($parentFolder, $file);
                }
                //  IF twig view file
                elseif((strpos($parentFolder, '/views/') !== false) && ($extension == 'twig'))
                {
                    self::extendVoltViewFile($parentFolder, $file);
                }
                //  IF controller file
                elseif((strpos($parentFolder, '/controllers/') !== false) && ($extension == 'php'))
                {
                    self::extendControllerFile($parentFolder, $file);
                }
                //  IF office file
                elseif((strpos($parentFolder, '/objects/') !== false) && ($extension == 'php'))
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
    }

    public static function extendVoltViewFile($corePath, $file)
    {
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
    }

    public static function extendControllerFile($corePath, $file)
    {
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
    }

    public static function extendObjectFile($corePath, $file)
    {
        //  if file exists
        if(file_exists($corePath.$file))
        {
            //  ensure destination path
            $projectPath        = str_replace('__core__', 'project', $corePath);
            File::ensurePath($projectPath);

            //  set template vars
            $namespace          = File::getNamespaceFromFile($corePath.$file);
            $class              = str_replace('Core.php', '', $file);
            $projectFile        = str_replace('Core', '', $file);
            $coreClass          = str_replace('.php', '', $file);
            $classPath          = $namespace.'\\'.$coreClass;
            $methodPhpArray     = array();

            //  make object
            $reflection         = new ReflectionClass(new $classPath());

            //  get methods
            $methods['all']         = $reflection->getMethods();
            $methods['abstract']    = $reflection->getMethods(ReflectionMethod::IS_ABSTRACT);
            $methods['final']       = $reflection->getMethods(ReflectionMethod::IS_FINAL);
            $methods['public']      = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            $methods['private']     = $reflection->getMethods(ReflectionMethod::IS_PRIVATE);
            $methods['protected']   = $reflection->getMethods(ReflectionMethod::IS_PROTECTED);
            $methods['static']      = $reflection->getMethods(ReflectionMethod::IS_STATIC);

            foreach($methods['all'] AS $method)
            {
                //  build method type string
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

class Ext extends Extender {}