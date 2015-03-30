<?php

class News
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('News');
		$view->eyecatch('News', 'A blog about game development, software, and technology.');
		$view->banner('blog short');

		// Get articles from database
		$articleReader = new ArticleReader();
		$articles = $articleReader->getManyArticles();

		// Get news from twitter
		$twitterReader = new TwitterReader();
		$tweets = $twitterReader->getTweets();

		foreach($tweets as $key=>$tweet)
		{
			if(isset($tweet->text))
			{
				$tweetHtml = TwitterFormatter::renderTweet($tweet);
				$view->addSingleHTMLColumn($tweetHtml);
			}
		}

		foreach($articles as $key=>$article)
		{
			$content = $article->renderFullArticle();
			$view->addSingleHTMLColumn($content);
		}
		
		$view->render();
	}
}
