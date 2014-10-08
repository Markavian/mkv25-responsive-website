<?php

class Showcase
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Showcase');
		$view->eyecatch('Arm yourself', 'Games, prototypes, and fun things...');
		$view->banner('showcase');
		
		$view->addSingleColumn('Showcase is waiting for the editor to do a write up.');
		
		$view->render();
	}
}