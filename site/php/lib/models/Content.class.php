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
		$parsedown = new Parsedown();
		$markdownHtml = $parsedown->text($this->content);
		
		$output = $markdownHtml;

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
