<?php

class DefaultView
{
	var $title = 'Default View';
	var $content = array();

	public function responseCode($code, $message='')
	{
		header("HTTP/1.0 $code $message");

		$this->title = "$code - $message";

		$this->content[] = "<h1>HTTP/1.0 $code</h1>";
		$this->content[] = "<h2>$message</h2>";
	}

	public function routeInfo()
	{
		$basePath = Environment::getEnvironmentVariable('MKV25_SITE_BASE', '/');

		$this->content[] = "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
		$this->content[] = "<p>Request URL: " . $_SERVER['REQUEST_URI'] . "</p>";
		$this->content[] = "<p>Base Path: $basePath</p>";

		$this->content[] = Request::Test($basePath, $_SERVER['REQUEST_URI']);
	}

	public function displayException($exception)
	{
		$this->content[] = "<p>Exception: " . $exception->getMessage() . "</p>";
	}

	public function render()
	{
		$NL = "\n";

		$documentHeader = array('<html>', '<head>', "<title>$this->title</title>", '</head>', '<body>');
		$documentBody = $this->content;
		$documentFooter = array('</body></html>');

		$output = '<!DOCTYPE html>';
		$output .= implode($NL, $documentHeader);
		$output .= implode($NL, $documentBody);
		$output .= implode($NL, $documentFooter);

		return $output;
	}
}
