<?php

class Showcase
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Showcase');
		$view->eyecatch('Showcase', 'Games, prototypes, and fun things...');
		$view->banner('showcase short');
		
		$view->addSingleColumn('Showcase is waiting for the editor to do a write up.');

		$scrapbookLink = Content::load('site/content/scrapbook-link.content.md');
		$view->addSingleColumn($scrapbookLink);
		
		$view->render();
	}
}