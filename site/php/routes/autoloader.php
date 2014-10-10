<?php

function autoload_mkv25($className)
{
	if (load_class('../lib/controllers/', $className))
	{
		return;
	}
	
	if (load_class('../lib/models/', $className))
	{
		return;
	}
	
	if (load_class('../lib/views/', $className))
	{
		return;
	}
	
	if (load_class('../lib/helpers/', $className))
	{
		return;
	}
}

function autoload_parsedown($className)
{
	if (load_class('../lib/external/parsedown/', $className, '.php'))
	{
		return;
	}
}

spl_autoload_register("autoload_mkv25");
spl_autoload_register("autoload_parsedown");

function load_class($folder, $className, $extention='.class.php')
{
	$path = $folder . $className . $extention;
	if (file_exists($path))
	{
		require_once($path);
		return true;
	}
	
	return false;
}