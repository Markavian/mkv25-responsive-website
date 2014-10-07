<?php

class Blog
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Blog');
		$view->eyecatch('Thoughts about the world', 'A blog about game development, software, and technology.');
		
		$view->addSingleColumn('Blog content not wired up to database.');
		
		$view->render();
	}
}