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

		$firstFeature = Content::load('content/features/featured-game-1.content.md');
		$secondFeature = Content::load('content/features/featured-game-2.content.md');
		$view->addColumns($firstFeature, $secondFeature);
		
		$showcaseContent = Content::load('content/games.content.md');
		$view->addColumns($showcaseContent);

		$thirdFeature = Content::load('content/features/featured-game-3.content.md');
		$fourthFeature = Content::load('content/features/featured-game-4.content.md');
		$fifthFeature = Content::load('content/features/featured-game-5.content.md');
		$view->addColumns($thirdFeature, $fourthFeature, $fifthFeature);

		$sixthFeature = Content::load('content/features/featured-game-6.content.md');
		$seventhFeature = Content::load('content/features/featured-game-7.content.md');
		$eighthFeature = Content::load('content/features/featured-game-8.content.md');
		$view->addColumns($sixthFeature, $seventhFeature, $eighthFeature);

		$scrapbookLink = Content::load('content/scrapbook-link.content.md');
		$view->addColumns($scrapbookLink);
		
		return $view->render();
	}
}