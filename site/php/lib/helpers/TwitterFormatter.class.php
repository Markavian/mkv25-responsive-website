<?php

class TwitterFormatter
{
	public static function renderTweet($tweet)
	{
		ob_start();

		$content = $tweet->text;
		$postTime = strtotime($tweet->created_at);
		$postDate = date("Y/m/d h:i:s", $postTime);
		$rawTweet = print_r($tweet, true);

		$parsedown = new Parsedown();
		$content = TwitterFormatter::preserveHashTags($content);
		$content = $parsedown->text($content);
		$content = TwitterFormatter::reverseHashTags($content);

		$media = TwitterFormatter::renderMediaForTweet($tweet);

		$rawUser = false;
		$iconUrl = '//mkv25.net/site/icons/planets_icon.png';
		
		if(isset($tweet->user->id))
		{
			$twitterReader = new TwitterReader();
			$userInfo = $twitterReader->getTwitterUser($tweet->user->id);
			$iconUrl = $userInfo->profile_image_url_https;
			$iconUrl = TwitterFormatter::removeProtocolFrom($iconUrl);
			$iconUrl = TwitterFormatter::removeScaleFromImageName($iconUrl);

			$rawUser = print_r($userInfo, true);

			$screenName = $userInfo->screen_name;
		}

		echo <<<END
			<block class="right">
				<iconlist>
					<icon style="background: url('$iconUrl') no-repeat center center; background-size:cover" title="Posted by $screenName"></icon>
				</iconlist>
				<date>$postDate</date>
			</block>
			<heading>Twitter Update</heading>
			<tweet>
				$content
				$media
			</tweet>
END;

		return ob_get_clean();

	}

	private static function removeProtocolFrom($url)
	{
		return str_replace("https://", "//", $url);
	}

	private static function removeScaleFromImageName($url)
	{
		return str_replace("_normal.png", ".png", $url);
	}

	public static function renderMediaForTweet($tweet)
	{
		ob_start();

		if(isset($tweet->extended_entities))
		{
			$mediaEntities = $tweet->extended_entities->media;
			foreach($mediaEntities as $key=>$mediaItem)
			{
				echo <<<END
					<media><img src="$mediaItem->media_url_https" /></media>
END;
			}
		}

		return ob_get_clean();
	}

	private static function preserveHashTags($content) {
		return str_replace("#", "[HASH]", $content);
	}

	private static function reverseHashTags($content) {
		return str_replace("[HASH]", "#", $content);
	}

}
