<?php

class Block
{
    static $folderArray = array();
    
    public static function getBlockFolderArray()
    {
        //  INIT
        $blockSuffix    = 'block-';
        $allBlockPath   = '../../';
        $folderArray    = array();
        
        //  GET directory
        $d = dir($allBlockPath);
        
        //  FOR each entry
        while (false !== ($entry = $d->read())) 
        {    
            //  IF entry is valid block folder
            if($entry != '.'
            && $entry != '..'
            && $entry != 'block-host'
            && is_dir($allBlockPath.$entry)
            && strpos($entry, $blockSuffix) == 0)
            {
                //  STORE block folder
                self::$folderArray[]  = $allBlockPath.$entry.'/';
            }
        }
        
        //  CLOSE directory
        $d->close();
        
        return self::$folderArray;
    }
    
    public static function available()
    {
        
    }
}