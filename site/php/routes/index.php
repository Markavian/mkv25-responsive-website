<?php

require('./autoloader.php');
require('./routes.php');

$basePath = getenv('MKV25_SITE_BASE');
if($basePath == '')
{
	$basePath = '/';
}

$environment = getenv('SERVER_ENV');
$environment = $environment ? $environment : 'stage';

require('./environment/environment.' . $environment . '.config.php');

$request = new Request($basePath);
$path = $request->path ? $request->path : 'home';
$section = $request->section;

if(isset($routes[$path]))
{
	renderPath($routes[$path], $request);
}
else if(isset($routes[$section]))
{
	renderPath($routes[$section], $request);
}
else
{
	$view = new DefaultView();
	$view->responseCode(404, 'File not found, no route set');
	$view->routeInfo();
}

function renderPath($controllerClass, $request)
{
	session_start();
	$auth = new Auth();
	
	try
	{
		$controller = new $controllerClass($request);
	}
	catch (Exception $exception)
	{
		$view = new DefaultView();
		$view->responseCode(501, 'Error creating controller ' . $controllerClass);
		$view->displayException($exception);
		$view->routeInfo();
	}
}