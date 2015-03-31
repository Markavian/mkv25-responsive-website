<?php

class TwitterFormatter
{
	public static function renderTweet($tweet)
	{
		ob_start();

		$displayContent = $tweet->text;
		$postTime = strtotime($tweet->created_at);
		$postDate = date("Y/m/d h:i:s", $postTime);
		$rawTweet = print_r($tweet, true);

		$parsedown = new Parsedown();
		$displayContent = TwitterFormatter::preserveHashTags($displayContent);
		$displayContent = $parsedown->text($displayContent);
		$displayContent = TwitterFormatter::reverseHashTags($displayContent);

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

		$mediaItems = TwitterFormatter::getMediaForTweet($tweet);
		$displayMedia = TwitterFormatter::renderMedia($mediaItems);

		if(count($mediaItems) > 0) {
			$iconUrl = $mediaItems[0]->media_url_https;
		}

		echo <<<END
			<block class="right">
				<icon style="background-image: url('$iconUrl')" title="Posted by $screenName"></icon><br />
				<date>$postDate</date>
			</block>
			<heading>Twitter Update</heading>
			<tweet>
				$displayContent
				$displayMedia
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

	public static function renderMedia($media)
	{
		ob_start();

		foreach($media as $key=>$mediaItem)
		{
			echo <<<END
				<media><img src="$mediaItem->media_url_https" /></media>
END;
		}

		return ob_get_clean();
	}

	public static function getMediaForTweet($tweet)
	{
		$media = Array();

		if(isset($tweet->extended_entities))
		{
			$mediaEntities = $tweet->extended_entities->media;
			foreach($mediaEntities as $key=>$mediaItem)
			{
				$media[] = $mediaItem;
			}
		}

		return $media;
	}

	private static function preserveHashTags($content) {
		return str_replace("#", "[HASH]", $content);
	}

	private static function reverseHashTags($content) {
		return str_replace("[HASH]", "#", $content);
	}

}
