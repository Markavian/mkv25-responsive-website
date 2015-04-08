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

	public function addColumns()
	{
		$columns = func_get_args();

		if(!is_array($columns))
		{
			$content = $columns;
			$this->addSingleColumn($content);
		}
		else
		{
			$columnCount = count($columns);
			$cssClass = TemplateView::getCssClassForNumberOfColumns($columnCount);

			$count = 0;
			foreach($columns as $content)
			{
				$first = ($count == 0);

				$this->addColumn($content, $cssClass, $first);

				$count++;
			}
		}
	}

	private static function getCssClassForNumberOfColumns($number)
	{
		$classes = array(
			1 => 'single',
			2 => 'double',
			3 => 'triple',
			4 => 'quad'
		);

		$result = (isset($classes[$number])) ? $classes[$number] : 'single';

		return $result;
	}
	
	public function addSingleColumn($content)
	{
		$this->addColumn($content, 'single', true);
	}
	
	public function addDoubleColumns($first, $second)
	{
		$columns = array($first, $second);
		$this->addColumns($columns);
	}
	
	public function addTripleColumns($first, $second, $third)
	{
		$columns = array($first, $second, $third);
		$this->addColumns($columns);
	}
	
	public function addQuadColumns($first, $second, $third, $fourth)
	{
		$columns = array($first, $second, $third, $fourth);
		$this->addColumns($columns);
	}
	
	private function addColumn($content, $type, $first=false)
	{
		$column = new ContentColumn($content, $type, $first);
		$this->columns[] = $column;
	}
	
	public function render()
	{
		$this->template->set('{COLUMN_BODY}', $this->renderColumns());

		$this->addPageStats();
		
		return $this->template->expand();
	}
	
	private function addPageStats()
	{
		$this->template->set('{EXECUTION_TIME}', reportExecutionTime());
		$this->template->set('{CACHE_READS}', FileCache::$reads);
		$this->template->set('{CACHE_WRITES}', FileCache::$writes);
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