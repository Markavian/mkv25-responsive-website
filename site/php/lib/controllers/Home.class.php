<?php

class Home
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->loadTemplate('index.template.html');
		$view->render();
	}
}