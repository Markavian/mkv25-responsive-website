<?php

class Home
{
	public function __construct($request)
	{
		global $HOME;

		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title($HOME['title']);
		$view->eyecatch('Coding at the core', 'Making games, discussing software, sharing source.');
		$view->banner('home');

		$view->addSingleColumn('Home sweet home');
		$view->addDoubleColumns('Priorities', 'Requests');
		$view->addTripleColumns('Priorities', 'Requests', 'Rewards');
		$view->addQuadColumns('Luminosity', 'Glare', 'Bloom', 'Potent');

		$view->render();
	}
}