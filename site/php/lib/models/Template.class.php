<?php

class Template
{
	const TEMPLATE_DIRECTORY = '../../../templates/';

	var $template;
	var $keys;
	
	public function __construct($path)
	{
		$keys = array();
		
		$this->loadTemplate($path);
	}
	
	private function loadTemplate($path)
	{
		$fullPath = __DIR__ . '/' . Template::TEMPLATE_DIRECTORY . $path;
		if (file_exists($fullPath))
		{
			$this->template = file_get_contents($fullPath);
		}
		else
		{
			throw new Exception('Template not found on path: ' . $fullPath);
		}
	}
	
	public function set($key, $value)
	{
		$this->keys[$key] = $value;
	}
	
	public function expand()
	{
		$output = $this->template;
		$output = Template::removeTabs($output);
		
		if(count($this->keys) > 0)
		{
			foreach($this->keys as $key => $value)
			{
				$output = str_replace($key, $value, $output);
			}
		}
		
		return $output;
	}
	
	public static function removeTabs($string)
	{
		return str_replace("\t", "  ", $string);
	}

	public static function load($path, $keys=false)
	{
		$template = new Template($path);

		if($keys && count($keys) > 0)
		{
			foreach($keys as $key => $value)
			{
				$template->set($key, $value);
			}
		}

		return $template->expand();
	}
}
