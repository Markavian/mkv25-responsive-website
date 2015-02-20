<?php

require('./autoloader.php');
require('./routes.php');

$basePath = getenv('MKV25_SITE_BASE');
if($basePath == '')
{
	$basePath = '/';
}

$environment = getenv('SERVER_ENV');
require('./environment/environment.' . $environment . '.config.php');

$request = new Request($basePath);
$path = $request->path ? $request->path : 'home';

if(isset($routes[$path]))
{
	$controllerClass = $routes[$path];
	
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
else
{
	$view = new DefaultView();
	$view->responseCode(404, 'File not found, no route set');
	$view->routeInfo();
}