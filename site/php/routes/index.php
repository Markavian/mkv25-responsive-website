<?php

require('./autoloader.php');

$routes = array();

$routes['home'] = 'Home';
$routes['subpath/test'] = 'Home';

$basePath = getenv('MKV25_SITE_BASE');

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