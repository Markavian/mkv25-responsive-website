<?php

class Home
{
	public function render($request)
	{
		$TITLE = Environment::get('TITLE');

		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title($TITLE);
		$view->eyecatch('Coding at the core', 'Making games, discussing software, sharing source.');
		$view->banner('home');

		$view->addSingleColumn('Home sweet home');
		$view->addDoubleColumns('Priorities', 'Requests');
		$view->addTripleColumns('Priorities', 'Requests', 'Rewards');
		$view->addQuadColumns('Luminosity', 'Glare', 'Bloom', 'Potent');

		echo $view->render();
	}
}