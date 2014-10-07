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
		echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
		echo "<p>Request URL: " . $_SERVER['REQUEST_URI'] . "</p>";

		$request = new Request('');

		$sink = Request::get('sink', false);
		echo "<p>$sink</p>";

		Request::Test('/mkv25/', $_SERVER['REQUEST_URI']);
	}
}
