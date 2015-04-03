<?php

class TemplateView
{
	var $template;
	var $columns;
	
	public function __construct()
	{
		$REQUEST = Environment::get('REQUEST');

		$this->keys = array();
		$this->columns = array();
		
		$this->template = new Template('index.template.html');
		
		$this->title();
		$this->description();
		$this->baseUrl($REQUEST->base);
		$this->banner();

		$auth = new Auth();
		$user = $auth->getCurrentUser();
		$signInLinks = new SignInLinks($user);
		$this->template->set('{SIGN_IN_LINKS}', $signInLinks->render());
	}
	
	public function title($value='No title')
	{
		$this->template->set('{TITLE}', $value);
	}
	
	public function description($value='No description')
	{
		$this->template->set('{META_DESCRIPTION}', $value);
	}
	
	public function eyecatch($title, $description)
	{
		$this->template->set('{EYECATCH_TITLE}', $title);
		$this->template->set('{EYECATCH_DESCRIPTION}', $description);
	}
	
	public function banner($value='')
	{
		$this->template->set('{BANNER_TYPE}', $value);
	}
	
	public function baseUrl($value='')
	{
		$this->template->set('{BASE_URL}', $value);
	}
	
	public function addSingleColumn($content)
	{
		$this->addColumn($content, 'single', true);
	}
	
	public function addDoubleColumns($first, $second)
	{
		$this->addColumn($first,  'double', true);
		$this->addColumn($second, 'double');
	}
	
	public function addTripleColumns($first, $second, $third)
	{
		$this->addColumn($first,  'triple', true);
		$this->addColumn($second, 'triple');
		$this->addColumn($third,  'triple');
	}
	
	public function addQuadColumns($first, $second, $third, $fourth)
	{
		$this->addColumn($first,  'quad', true);
		$this->addColumn($second, 'quad');
		$this->addColumn($third,  'quad');
		$this->addColumn($fourth, 'quad');
	}
	
	private function addColumn($content, $type, $first=false)
	{
		$column = new ContentColumn($content, $type, $first);
		$this->columns[] = $column;
	}
	
	public function render()
	{
		$this->template->set('{COLUMN_BODY}', $this->renderColumns());
		
		echo $this->template->expand();
	}
	
	function renderColumns()
	{
		$output = '';
		
		foreach ($this->columns as $index => $column)
		{
			$output .= $column->render();
		}
		
		return $output;
	}
}