<?php

class Home
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->addSingleColumn('Home sweet home.');
		$view->addDoubleColumns('Priorities', 'Requests');
		$view->addTripleColumns('Priorities', 'Requests', 'Rewards');
		$view->addQuadColumns('Luminosity', 'Glare', 'Bloom', 'Potent');
		$view->render();
	}
}