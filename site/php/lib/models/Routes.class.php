<?php

class Routes
{
	private static $routes;

	public static function addRoute($route, $controllerClassName)
	{
		Routes::initialise();

		Routes::$routes[$route] = $controllerClassName;
	}

	public static function isRouteConfigured($route)
	{
		return isset(Routes::$routes[$route]);
	}

	public static function getControllerForRoute($route)
	{
		$controller = false;
		$controllerClass = false;

		// Look for controller that matches route
		if(Routes::isRouteConfigured($route))
		{
			$controllerClass = Routes::$routes[$route];

			// Try and create instance of controller
			if($controllerClass)
			{
				$controller = new $controllerClass();
			}
		}

		return $controller;
	}

	private static function initialise()
	{
		if(!Routes::$routes)
		{
			Routes::$routes = array();
		}
	}
}