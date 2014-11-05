<?php

namespace Host\Object;

/**
 *  This is the Application block. Load this thing up with as much functionality 
 *  as you want, because there will only ever be one of it instanciated
 */
class AppBlockCore 
{
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
                $folderArray[]  = $allBlockPath.$entry.'/';
            }
        }
        
        //  CLOSE directory
        $d->close();
        
        return $folderArray;
    }
}