<?php

class DefaultView
{
	public function responseCode($code, $message='')
	{
		header("HTTP/1.0 $code $message");
		
		echo "<h1>HTTP/1.0 $code</h1>";
		echo "<h2>$message</h2>";
	}

	public function routeInfo()
	{
		$basePath = getenv('MKV25_SITE_BASE');
		
		echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
		echo "<p>Request URL: " . $_SERVER['REQUEST_URI'] . "</p>";
		echo "<p>Base Path: $basePath</p>";
		
		Request::Test($basePath, $_SERVER['REQUEST_URI']);
	}
	
	public function displayException($exception)
	{
		echo "<p>Exception: " . $exception->getMessage() . "</p>";
	}
}
