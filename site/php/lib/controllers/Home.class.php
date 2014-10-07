<?php

class Home
{
	public function __construct($request)
	{
		$view = new DefaultView();
		$view->responseCode(200, "Request OK");
		$view->routeInfo();
	}
}