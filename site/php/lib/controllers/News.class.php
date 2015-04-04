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
		
		$numberOfArticles = 0;
		$numberOfTweets = 0;
		
		if (is_array($tweets))
		{
			foreach($tweets as $key=>$tweet)
			{
				if(isset($tweet->text))
				{
					$tweetHtml = TwitterFormatter::renderTweet($tweet);
					$view->addSingleColumn($tweetHtml);
				}
				$numberOfTweets++;
			}
		}

		if (is_array($articles))
		{
			foreach($articles as $key=>$article)
			{
				$content = $article->renderFullArticle();
				$view->addSingleColumn($content);
				
				$numberOfArticles++;
			}
		}
		
		if ($numberOfTweets == 0 && $numberOfArticles == 0)
		{
			$view->addSingleColumn("No news available at this time.");
		}
		
		$view->render();
	}
}
