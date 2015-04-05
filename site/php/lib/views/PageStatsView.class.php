<?php

class PageStatsView
{
	public static function addPageStats()
	{
		$executionTime = reportExecutionTime();
		
		PageStatsView::setHeader("mkv25-page-execution-time", $executionTime);
		PageStatsView::setHeader("mkv25-filecache-reads", FileCache::$reads);
		PageStatsView::setHeader("mkv25-filecache-writes", FileCache::$writes);
	}
	
	private static function setHeader($key, $value)
	{
		$string = sprintf("%s: %s", $key, $value);
		header($string);
	}
}