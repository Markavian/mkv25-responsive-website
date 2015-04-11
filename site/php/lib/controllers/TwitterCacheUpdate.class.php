<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterCacheUpdate
{
	var $twitterOAuthConnection;

	public function __construct()
	{
		$TWITTER = Environment::get('TWITTER');

		$consumerKey = $TWITTER['CONSUMER_KEY'];
		$consumerSecret = $TWITTER['CONSUMER_SECRET'];
		$accessToken = $TWITTER['ACCESS_TOKEN'];
		$accessTokenSecret = $TWITTER['ACCESS_TOKEN_SECRET'];

		if ($consumerKey && $consumerSecret && $accessToken && $accessTokenSecret)
		{
			$this->twitterOAuthConnection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
		}
	}

	public function render($request)
	{
		$field = $request->page_field;
		$value = $request->page_value;

		if($field && $value)
		{
			$message = "I'm making shit up: $field for $value";
			$result = $this->renderResponse($message);
		}
		else if($field)
		{
			$message = "Method not allowed: $field";
			$result = $this->render501NotSupportedResponse($message);
		}
		else
		{
			$message = "No method specified";
			$result = $this->render501NotSupportedResponse($message);
		}

		return $result;
	}

	private function render501NotSupportedResponse($message)
	{
		$view = new DefaultView();
		$view->responseCode(501, $message);
		
		return $view->render();
	}

	private function render202AcceptedResponse($message)
	{
		$view = new DefaultView();
		$view->responseCode(202, $message);
		
		return $view->render();
	}
}