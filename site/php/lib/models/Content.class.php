<?php

class Content
{
	const CONTENT_BASE_DIRECTORY = '../../';

	var $content;

	public function __construct($name)
	{
		$keys = array();

		$this->loadContent($name);
	}

	private function loadContent($name)
	{
		$fullPath = Content::generatePath($name);

		if (Content::exists($name))
		{
			$this->content = file_get_contents($fullPath);
		}
		else
		{
			throw new Exception("Content '$name' not found on path: $fullPath");
		}
	}

	public function render()
	{
		// Find and escape media tags
		$mediaFormatter = new MediaFormatter();
		$escapedMediaContent = $mediaFormatter->createMediaKeys($this->content);

		// Parse remaining content using Parsedown rules
		$parsedown = new Parsedown();
		$markdownHtml = $parsedown->text($escapedMediaContent);

		// Reinsert media tags as HTML blocks
		$replacedMediaHTML = $mediaFormatter->replaceMediaKeys($markdownHtml);

		$output = $replacedMediaHTML;

		return $output;
	}

	private static function generatePath($name)
	{
		return sprintf("%s%s", Content::CONTENT_BASE_DIRECTORY, $name);
	}

	public static function load($name)
	{
		$content = new Content($name);

		return $content->render();
	}

	public static function exists($name)
	{
		$fullPath = Content::generatePath($name);

		return file_exists($fullPath);
	}
}
