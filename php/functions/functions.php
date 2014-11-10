<?php

function debug($var)
{
	if(is_object($var) or is_array($var))
	{
		echo('<pre>'.print_r($var, true).'</pre>');
	}
	else
	{
		echo('<p>'.$var.'</p>');
	}
}

function thisIsWindows()
{
	//	init
	$thisIsWindows = false;

	if (strncasecmp(PHP_OS, 'WIN', 3) == 0) 
	{
    	$thisIsWindows = true;
	} 

	return $thisIsWindows;
}

function handle2upperHandle($in)
{
	//	convert _ or - to space
	//	upper case the words
	//	remove the spaces
	//	ie. my-awesome-handle becomes MyAwesomeHandle
	return str_replace(' ','',ucwords(str_replace(array('_','-'),' ',$in)));
}

function getRootFromServerGlobal($serverGlobalArray)
{
	//	init
	$rootPath 			= '/';
	$rootPathArray		= array();
	$scriptPathArray	= explode('/', $serverGlobalArray['SCRIPT_NAME']);

	//	process path parts
	foreach($scriptPathArray AS $scriptPathPart)
	{
		//	if part is core folder, break out of loop
		if($scriptPathPart == CORE_FOLDER)	
		{
			break;
		}
		//	else store path part
		elseif(strlen(trim($scriptPathPart)))
		{
			$rootPathArray[]	= $scriptPathPart;
		}
	}

	//	if root path parts discovered
	if(sizeof($rootPathArray))
	{
		//	set root path
		$rootPath 	= '/'.implode('/', $rootPathArray).'/';
	}

	return $rootPath;
}