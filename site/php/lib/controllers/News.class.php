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
                    // $view->addSingleColumn('<code><pre>' .  htmlspecialchars(json_encode($tweet, JSON_PRETTY_PRINT)) . '</pre></code>');
				}
				$numberOfTweets++;
			}
		}

		if ($numberOfTweets === 0)
		{
			$view->addSingleColumn("No news available at this time.");
			$view->addSingleColumn('Check the twitter feed by <a href="https://twitter.com/Markavian">visiting twitter @Markavian</a>.');
		}
		else
		{
			$view->addSingleColumn('View older news, or get in touch by <a href="https://twitter.com/Markavian">visiting twitter @Markavian</a>.');
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
