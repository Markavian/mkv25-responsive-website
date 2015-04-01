<?php

class Router
{
	public static function handleRouting()
	{
		Environment::initialise();

		$siteRootUrl = Environment::getEnvironmentVariable('MKV25_SITE_BASE', '/');

		$request = new Request($siteRootUrl);
		$section = $request->section;
		$path = $request->path ? $request->path : 'home';

		Environment::register('REQUEST', $request);
		Environment::register('SITE_ROOT_URL', $siteRootUrl);

		if(Routes::isRouteConfigured($path))
		{
			Router::renderPath($path, $request);
		}
		else if(Routes::isRouteConfigured($section))
		{
			Router::renderPath($section, $request);
		}
		else
		{
			$view = new DefaultView();
			$view->responseCode(404, 'File not found, no route set');
			$view->routeInfo();
		}
	}

	private static function renderPath($route, $request)
	{
		session_start();
		$auth = new Auth();
		
		try
		{
			$controller = Routes::getControllerForRoute($route, $request);
		}
		catch (Exception $exception)
		{
			$view = new DefaultView();
			$view->responseCode(501, 'Error creating controller for route: ' . $route);
			$view->displayException($exception);
			$view->routeInfo();
		}
	}
}
