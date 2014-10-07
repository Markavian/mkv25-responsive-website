<?php

class TemplateView
{
	const TEMPLATE_DIRECTORY = '../../../';

	var $template;
	var $keys;
	
	public function __construct()
	{
		$this->keys = array();
		
		$this->set('{TITLE}', 'No Title');
	}
	
	public function loadTemplate($name)
	{
		$path = TemplateView::TEMPLATE_DIRECTORY . $name;
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
	
	public function render()
	{
		$output = $this->template;
		$output = TemplateView::removeTabs($output);
		
		foreach($this->keys as $key => $value)
		{
			$output = str_replace($key, $value, $output);
		}
		
		echo $output;
	}
	
	public static function removeTabs($string)
	{
		return str_replace("\t", "  ", $string);
	}
}