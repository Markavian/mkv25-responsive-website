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
	
	throw new MissingException('Unable to load class ' . $name);
}

function load_class($folder, $className)
{
	$path = $folder . $className . '.class.php';
	if (file_exists($path))
	{
		require_once($path);
		return true;
	}
	
	return false;
}
