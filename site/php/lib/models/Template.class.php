<?php

class Template
{
	const TEMPLATE_DIRECTORY = '../../../';

	var $template;
	var $keys;
	
	public function __construct($name)
	{
		$keys = array();
		
		$this->load($name);
	}
	
	function load($name)
	{
		$path = Template::TEMPLATE_DIRECTORY . $name;
		if (file_exists($path))
		{
			$this->template = file_get_contents($path);
		}
		else
		{
			throw new Exception('Template not found: ' . $name);
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
		
		foreach($this->keys as $key => $value)
		{
			$output = str_replace($key, $value, $output);
		}
		
		return $output;
	}
	
	public static function removeTabs($string)
	{
		return str_replace("\t", "  ", $string);
	}
}
