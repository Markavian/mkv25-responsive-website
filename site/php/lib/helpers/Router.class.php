<?php

class Router
{
	public static function handleRouting()
	{
		Environment::initialise();

		$siteRootUrl = Environment::getEnvironmentVariable('MKV25_SITE_BASE', '/');

		$request = new Request($siteRootUrl);
		$folder = $request->folder;
		$path = $request->path ? $request->path : 'home';

		Environment::register('REQUEST', $request);
		Environment::register('SITE_ROOT_URL', $siteRootUrl);
		if(Routes::isRouteConfigured($path))
		{
			Router::renderPath($path, $request);
		}
		else if(Routes::isRouteConfigured($folder))
		{
			Router::renderPath($folder, $request);
		}
		else
		{
			$view = new DefaultView();
			$view->responseCode(404, 'File not found, no route set');
			$view->routeInfo();
			
			print $view->render();
		}
	}

	private static function renderPath($route, $request)
	{
		// Buffer command to capture stray print and echo statements
		ob_start();
			
		session_start();
		$auth = new Auth();
		
		/* TODO: Wrap render in proxy file cache - if appropriate - careful about
		$proxyCache = ProxyCache::create($this, Time::oneMinute()->inSeconds());
		$proxyCache-> render($request);
		*/
			
		try
		{
			$controller = Routes::getControllerForRoute($route);
			
			$pageContent = $controller->render($request);
			
			// Add additional page headers after render has completed
			PageStatsView::addPageStats();
		}
		catch (Exception $exception)
		{
			$view = new DefaultView();
			$view->responseCode(501, 'Error creating controller for route: ' . $route);
			$view->displayException($exception);
			$view->routeInfo();
			
			$pageContent = $view->render();
		}
			
		$echoedContent = ob_get_clean();
			
		// Return the page content to the user
		print $pageContent;
		
		// Include stray content as a comment at end of response
		if ($echoedContent)
		{
			print "<!-- $echoedContent -->";
		}
	}
}
