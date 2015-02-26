<?php

class Showcase
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Showcase');
		$view->eyecatch('Showcase', 'Games, prototypes, and fun things...');
		$view->banner('showcase short white-tint');
		
		$view->addSingleColumn('Showcase is waiting for the editor to do a write up.');
		
		$view->render();
	}
}