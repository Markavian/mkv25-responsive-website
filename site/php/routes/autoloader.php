<?php

function __autoload($className)
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
	
	throw new Exception('Unable to load class ' . $className);
}

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