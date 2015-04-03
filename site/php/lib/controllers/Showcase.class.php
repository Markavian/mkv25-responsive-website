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
		
		$showcaseContent = Content::load('content/showcase.content.md');
		$view->addSingleColumn($showcaseContent);

		$scrapbookLink = Content::load('content/scrapbook-link.content.md');
		$view->addSingleColumn($scrapbookLink);
		
		$view->render();
	}
}