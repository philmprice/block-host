<?php

namespace Model;

class ModuleCore extends \Model\Noun
{
	public $name;
	public $uid;
	public $publicUrl;
	public $adminUrl;

	public static function register($moduleData)
	{
		//	find module
		$module	= Module::findFirstByUid($moduleData['uid']);

		//	if module not found
		if(!$module)
		{
			//	create module
			$module 			= new Module();
			$module->name 		= $moduleData['name'];
			$module->uid 		= $moduleData['uid'];
			$module->publicUrl 	= $moduleData['publicUrl'];
			$module->adminUrl	= $moduleData['adminUrl'];
			$module->save();
		}
	}
	
}