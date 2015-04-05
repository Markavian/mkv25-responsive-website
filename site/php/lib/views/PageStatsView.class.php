<?php

class PageStatsView
{
	var $template;
	
	public function __construct()
	{
		$this->template = new Template('site/templates/page-stats.template.html');
	}
	
	private function addPageStats()
	{
		$this->template->set('{EXECUTION_TIME}', reportExecutionTime());
		$this->template->set('{CACHE_READS}', FileCache::$reads);
		$this->template->set('{CACHE_WRITES}', FileCache::$writes);
	}
	
	public function render()
	{
		$this->addPageStats();
		
		return $this->template->expand();
	}
}