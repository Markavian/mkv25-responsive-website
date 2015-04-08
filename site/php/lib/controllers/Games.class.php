<?php

class Games
{
	public function render($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Games');
		$view->eyecatch('Games', 'Games, prototypes, and fun things...');
		$view->banner('games short');

		$firstFeature = 'Featured game 1';
		$secondFeature = 'Featured game 2';
		$view->addColumns($firstFeature, $secondFeature);
		
		$showcaseContent = Content::load('content/games.content.md');
		$view->addColumns($showcaseContent);

		$thirdFeature = 'Featured game 3';
		$fourthFeature = 'Featured game 4';
		$fifthFeature = 'Featured game 5';
		$view->addColumns($thirdFeature, $fourthFeature, $fifthFeature);

		$sixthFeature = 'Featured game 6';
		$seventhFeature = 'Featured game 7';
		$eighthFeature = 'Featured game 8';
		$view->addColumns($sixthFeature, $seventhFeature, $eighthFeature);

		$scrapbookLink = Content::load('content/scrapbook-link.content.md');
		$view->addColumns($scrapbookLink);
		
		return $view->render();
	}
}