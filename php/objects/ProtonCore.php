<?php

namespace Host;

//	PROTON: a stateful object starter class
class ProtonCore
{
	var		$arrVar	= array();
	private $_Id	= null;

	public function __construct($Id)
	{
		$this->_Id = $Id;
		$this->Load();
	}
	
	public function Load()
	{
		//	INIT
		$this->EnsureVarMemory();
		
		//	LOAD from memory
		foreach($_SESSION['Proton'][$this->_Id]['var'] AS $name => $val)
		{
			$this->arrVar[$name]	= $val;
		}
	}

	public function Save()
	{
		//	INIT
		$this->EnsureVarMemory();
		
		//	SAVE to memory
		foreach($this->arrVar AS $Name => $Val)
		{
			$_SESSION['Proton'][$this->_Id]['var'][$Name]	= $Val;
		}
	}

	public function SaveVar($Name)
	{
		//	INIT
		$this->EnsureVarMemory();
		
		//	SAVE to memory
		$_SESSION['Proton'][$this->_Id]['var'][$Name]	= $this->arrVar[$Name];
	}
	
	public function ClearVar($Name)
	{
		//	INIT
		$this->EnsureVarMemory();
		
		//	UNSET in var array
		unset($this->arrVar[$Name]);
		
		//	UNSET in session
		unset($_SESSION['Proton'][$this->_Id]['var'][$Name]);
	}

	public function ClearAllVars()
	{
		//	INIT
		$this->EnsureVarMemory();
		
		foreach($this->arrVar AS $Name => $Value)
		{
			//	UNSET in var array
			unset($this->arrVar[$Name]);
		
			//	UNSET in session
			unset($_SESSION['Proton'][$this->_Id]['var'][$Name]);
		}
	}

	public function EnsureVarMemory()
	{
		//	ENSURE session
		if(!session_id()) session_start();
		
		//	IF no proton array
		if(!array_key_exists('Proton', $_SESSION))
		{
			//	CREATE it
			$_SESSION['Proton']	= array();
		}
		
		//	IF proton not there
		if(!array_key_exists($this->_Id, $_SESSION['Proton']))
		{
			//	START proton
			$_SESSION['Proton'][$this->_Id]	= array('var' => array());
		}
	}

	public function &__get($Name)
	{
		//	IF var exists
		if(array_key_exists($Name, $this->arrVar))
		{
			//	RETURN value
			switch($Name)
			{
				case '_id':
				case '_Id':
				case '_ID':
					return $this->_Id;

				default:
					return $this->arrVar[$Name];
			}
		}
		//	ELSE if var doesn't exist
		else
		{
			return false;
		}
	}

	public function __set($Name, $Value)
	{
		//	SET var
		$this->arrVar[$Name] = $Value;
		
		//	SAVE var state
		$this->SaveVar($Name);
	}
}

?>
