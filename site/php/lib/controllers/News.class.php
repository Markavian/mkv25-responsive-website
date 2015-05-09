<?php

class News
{
	public function render($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('News');
		$view->eyecatch('News', 'A blog about game development, software, and technology.');
		$view->banner('blog short');

		// Get news from twitter
		$twitterReader = new TwitterReader();
		$tweets = $twitterReader->getTweets();

		$numberOfTweets = 0;

		if (is_array($tweets))
		{
			$tweets = News::filterPersonalTweets($tweets);

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

		if ($numberOfTweets == 0)
		{
			$view->addSingleColumn("No news available at this time.");
		}

		return $view->render();
	}

	public static function filterPersonalTweets($tweets)
	{
		$filtered = array();
		foreach($tweets as $tweet)
		{
			if(startsWith($tweet->text, '@'))
			{
				// throw away
			}
			else
			{
				$filtered[] = $tweet;
			}
		}

		return $filtered;
	}
}
