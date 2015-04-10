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
		$view->banner('home short');

		$homeCodingAtTheCore = Content::load('content/home/coding-at-the-core.content.md');
		$view->addSingleColumn($homeCodingAtTheCore);

		$homeMakingGames = Content::load('content/home/making-games.content.md');
		$view->addSingleColumn($homeMakingGames);

		$firstFeature = Content::load('content/features/featured-game-1.content.md');
		$secondFeature = Content::load('content/features/featured-game-2.content.md');
		$thirdFeature = Content::load('content/features/featured-game-3.content.md');
		$fourthFeature = Content::load('content/features/featured-game-4.content.md');
		$view->addColumns($firstFeature, $secondFeature, $thirdFeature, $fourthFeature);

		$homeDiscussingSoftware = Content::load('content/home/discussing-software.content.md');
		$view->addSingleColumn($homeDiscussingSoftware);

		$homeSharingSource = Content::load('content/home/sharing-source.content.md');
		$view->addSingleColumn($homeSharingSource);

		return $view->render();
	}
}