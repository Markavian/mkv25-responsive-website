<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterReader
{
	public function getTwitterUser($userId)
	{
		$cacheName = "user.$userId";
		if(FileCache::doesNotExist($cacheName) || FileCache::ageOfCache($cacheName) > Time::oneHour()->inSeconds())
		{
			RemoteProcedureCall::makeRemoteCall("getTwitterUser", array($userId));
		}

		$userInfo = FileCache::readDataFromCache($cacheName);

		return $userInfo;
	}

	public function getTweets($userId="53020129")
	{
		$cacheName = "tweets";
		if(FileCache::doesNotExist($cacheName) || FileCache::ageOfCache($cacheName) > Time::tenMinutes()->inSeconds())
		{
			$connection = TwitterReader::createTwitterOAuthConnection();
			if(!$connection) return false;

			RemoteProcedureCall::makeRemoteCall("getTweetsForUser", array($userId));
		}

		$tweets = FileCache::readDataFromCache($cacheName);

		return $tweets;
	}

	public static function createTwitterOAuthConnection()
	{
		$connection = false;

		$TWITTER = Environment::get('TWITTER');

		$consumerKey = $TWITTER['CONSUMER_KEY'];
		$consumerSecret = $TWITTER['CONSUMER_SECRET'];
		$accessToken = $TWITTER['ACCESS_TOKEN'];
		$accessTokenSecret = $TWITTER['ACCESS_TOKEN_SECRET'];

		if ($consumerKey && $consumerSecret && $accessToken && $accessTokenSecret)
		{
			$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
		}

		return $connection;
	}
}