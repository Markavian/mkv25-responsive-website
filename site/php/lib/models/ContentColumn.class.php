<?php

class ContentColumn
{
	var $content;
	var $type;
	var $first;

	function __construct($content, $type, $first=false)
	{
		$this->content = $content;
		$this->type = $type;
		$this->first = $first;
	}
	
	public function render()
	{
		$template = new Template('site/templates/column.template.html');
		$cssClass = $this->type;
		$cssClass = ($this->first) ? $cssClass . ' first' : $cssClass;
		
		$template->set('{CSS_CLASS}', $cssClass);
		$template->set('{CONTENT}', $this->content);
		
		return $template->expand();
	}
}
