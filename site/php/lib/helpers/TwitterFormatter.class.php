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

		echo <<<END
			<heading>Twitter</heading>
			<tweet>
				<date>$postDate</date>
				<p>$content</p>
				$media
			</tweet>
END;

		return ob_get_clean();

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
