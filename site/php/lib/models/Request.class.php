<?php

/* To test this script, uncomment to run: */
// $test_url = (isset($_GET['test_url'])) ? $_GET['test_url'] : 'http://website.com/images/places/prague/001.jpg';
// Request::Test('http://website.com/', $test_url);

/**
 * Request class for processing friendly URL rewrites info useful variables.
 * @author John Beech aka Markavian
 * @website http://mkv25.net/
 */
class Request
{
	/* Quick ref: $uri, $base, $path, $subpath, $sections, $section, $page, $file, $page_field, $page_value */
	
	/**
	 * URI contains the full URI used to generate the rest of the variables
	 * e.g. http://website.com/images/places/prague/001.jpg
	 */
	var $uri;
	
	/**
	 * Base contains the base value specified when the object is created.
	 * e.g. http://website.com/
	 */
	var $base;
	
	/**
	 * Path contains the remainig path following the base
	 * e.g. images/places/prague/001.jpg
	 */
	var $path;
	
	/**
	 * Params contains any parameters appended to the end of the url, in the form:
	 *  http://website.com/page?key=value&pair=true
	 * or false if none set
	 */
	var $params;
	
	/**
	 * Section contains the first part of the url before the first forward slash /
	 * e.g. 'images'
	 */
	var $section;
	
	/**
	 * Subpath contains the remaining path following the section, without the file part
	 * e.g. places/prague/
	 */
	var $subpath;
	
	/** 
	 * Sections is an array that contains all parts of the URL separated by forward slash /
	 * e.g. array('images', 'places', 'prague', '001.jpg');
	 */
	var $sections;

	/**
	 * Page contains the contents of the last section in sections
	 * e.g. 001.jpg
	 */
	var $page;
	
	/**
	 * File contains the filename, or false if the URI ended in a forward slash /
	 * e.g. 001.jpg  
	 */
	var $file;

	/**
	 * Everything after the # in a URL
	 */
	var $hash;
	
	/**
	 * Page-Field contains the field part of the url:
	 *  http://website.com/field-Value
	 * or false if not in this format
	 */
	var $page_field;
	
	/**
	 * Page-Value contains the Value part of the url:
	 *  http://website.com/field-Value
	 * or false if not in this format
	 */
	var $page_value;
	
	function Request($base)
	{
		$this->uri = urldecode($_SERVER['REQUEST_URI']);
		$this->path = substr($this->uri, strlen($base));
		$p = explode('?', $this->path, 2);
		if (count($p) > 1) {
			$this->path = $p[0];
			$this->readParams($p[1]);
		}
		else
		{
			$this->params = array();
		}
		
		$s = explode("/", $this->path);
		
		$this->section = $s[0];
		$this->page = $s[count($s) - 1];
		$this->base = $base;
		$this->subpath = substr($this->path, strlen($this->section . '/'));
		$this->sections = $s;
		
		$s = $this->path;
		if(Request::read(strlen($s)-1, $s, '') == '/')
		{
			$this->file = false;
		}
		else
		{
			$this->file = $this->page;
			$this->subpath = substr($this->subpath, 0, strlen($this->subpath) - strlen($this->file));
		}
		
		if(($pos = strpos($this->page, '-', 0)) > 0)
		{
			$this->page_field = substr($this->page, 0, $pos);
			$this->page_value = substr($this->page, $pos + 1);
		}
		else
		{
			$this->page_field = false;
			$this->page_value = false;
		}
	}
	
	private function readParams($paramsString)
	{
		$params = array();
		if ($paramsString)
		{
			$pairs = explode('&', $paramsString);
			foreach ($pairs as $key => $value)
			{
				$pair = explode('=', $value, 2);
				$params[$pair[0]] = $pair[1];
			}
		}
		$this->params = $params;
	}
	
	function toString()
	{
		$NL = "\n";
		$output = '';
		
		$output .= ' base: (' . $this->base . ')' . $NL;
		$output .= ' uri: (' . $this->uri . ')' . $NL;
		$output .= ' path: (' . $this->path . ')' . $NL;
		
		$n = 0;
		$output .= ' params: Array(';
		foreach ($this->params as $key=>$value)
		{
			$output .= ($n > 0) ? ', ' : ' ';
			$output .= $key . ' : ' . $value;
			$n++;
		}
		$output .= ' )' . $NL;
		
		$output .= ' section: (' . $this->section . ')' . $NL;
		$output .= ' subpath: (' . $this->subpath . ')' . $NL;
		
		$output .= ' sections: Array(';
		for ($i=0; $i<count($this->sections); $i++)
		{
			$output .= ($i > 0) ? ', ' : ' ';
			$output .= $this->sections[$i];
		}
		$output .= ' )' . $NL;
		
		$output .= ' page: (' . $this->page . ')' . $NL;
		$output .= ' file: (' . $this->file . ')' . $NL;
		$output .= ' page_field: (' . $this->page_field . ')' . $NL;
		$output .= ' page_value: (' . $this->page_value . ')' . $NL;
		
		//$uri, $base, $path, $subpath, $sections, $section, $page, $file, $page_field, $page_value 
		
		return $output;
	}
	
	static function get($name, $default)
	{
		return Request::read($name, $_GET, $default);
	}
	
	static function read($name, $array, $default)
	{
		if (isset($array[$name]))
		{
			return $array[$name];
		}
		
		return $default;
	}
	
	static function test($base, $uri)
	{
		$_SERVER['REQUEST_URI'] = $uri;
		$request = new Request($base);
		
		return '<pre>' . $request->toString() . '</pre>';
	}
}
?>