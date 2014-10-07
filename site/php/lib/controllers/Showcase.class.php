<?php

class Showcase
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Showcase');
		$view->eyecatch('Welcome to the parade', 'Games, prototypes, and fun things...');
		
		$view->addSingleColumn('Showcase is waiting for the editor to do a write up.');
		
		$view->render();
	}
}