<?php

class Content
{
	const CONTENT_DIRECTORY = '../../../';

	var $content;
	
	public function __construct($path)
	{
		$keys = array();
		
		$this->loadContent($path);
	}
	
	private function loadContent($path)
	{
		$fullPath = Content::CONTENT_DIRECTORY . $path;
		if (file_exists($fullPath))
		{
			$this->content = file_get_contents($fullPath);
		}
		else
		{
			throw new Exception('Content not found on path: ' . $fullPath);
		}
	}
	
	public function render()
	{
		$parsedown = new Parsedown();
		$markdownHtml = $parsedown->text($this->content);
		
		$output = $markdownHtml;

		return $output;
	}

	public static function load($path)
	{
		$content = new Content($path);

		return $content->render();
	}
}
