<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterReader
{
	var $twitterOAuthConnection;

	public function __construct()
	{
		$TWITTER = Environment::get('TWITTER');

		$consumerKey = $TWITTER['CONSUMER_KEY'];
		$consumerSecret = $TWITTER['CONSUMER_SECRET'];
		$accessToken = $TWITTER['ACCESS_TOKEN'];
		$accessTokenSecret = $TWITTER['ACCESS_TOKEN_SECRET'];

		$this->twitterOAuthConnection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
	}

	public function getTwitterUser($userId)
	{
		$cachedUser = TwitterReader::getCachedUser($userId);
		if($cachedUser) {
			$userInfo = $cachedUser;
		}
		else
		{
			$userInfo = $this->twitterOAuthConnection->get("users/show", array("user_id" => $userId, "trim_user" => 1, "count" => 20));
			TwitterReader::storeUserInCache($userId, $userInfo);
		}

		return $userInfo;
	}

	private static function getCachedUser($userId)
	{
		$tweets = false;

		if(FileCache::ageOfCache("user.$userId") < Time::tenMinutes()->inSeconds())
		{
			$tweets = FileCache::readDataFromCache("user.$userId");
		}

		return $tweets;
	}

	private static function storeUserInCache($userId, $userInfo)
	{
		FileCache::storeDataInCache($userInfo, "user.$userId");
	}

	public function getTweets()
	{
		$myUserId = "53020129";

		$cachedTweets = TwitterReader::getCachedTweets();
		if($cachedTweets) {
			$tweets = $cachedTweets;
		}
		else
		{
			$tweets = $this->twitterOAuthConnection->get("statuses/user_timeline", array("user_id" => $myUserId, "trim_user" => 1, "count" => 20));
			TwitterReader::storeTweetsInCache($tweets);
		}

		return $tweets;
	}

	private static function getCachedTweets()
	{
		$tweets = false;

		if(FileCache::ageOfCache('tweets') < Time::tenMinutes()->inSeconds())
		{
			$tweets = FileCache::readDataFromCache('tweets');
		}

		return $tweets;
	}

	private static function storeTweetsInCache($tweets)
	{
		FileCache::storeDataInCache($tweets, 'tweets');
	}
}