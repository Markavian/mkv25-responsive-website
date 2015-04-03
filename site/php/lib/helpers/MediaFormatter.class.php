<?php

class MediaFormatter
{	
	var $mediaObjects;

	var $mediaRenderers;
	
	public function __construct()
	{
		$this->mediaObjects = Array();
		$this->mediaRenderers = Array(
			'EMBED_FLASH' => 'ArticleFormatter::renderFlashContent'
		);
	}

	public function createMediaKeys($content)
	{
		$result = '';
		$NL = "\n";

		$lines = explode($NL, $content);
		foreach($lines as $line)
		{
			if($line[0] == '{')
			{
				$line = rtrim($line);
				$result .= $this->escape($line) . $NL;
			}
			else
			{
				$result .= $line . $NL;
			}
		}

		return $result;
	}

	private function escape($line)
	{
		$result = $line;

		if(startsWith($line, '{EMBED_FLASH') && endsWith($line, "}"))
		{
			$result = $this->escapeFlashContent($line);
		}

		return $result;
	}

	private function escapeFlashContent($line)
	{
		$mediaCount = count($this->mediaObjects) + 1;
		$mediaKey = sprintf("PLACE HOLDER FOR FLASH MEDIA OBJECT %d", $mediaCount);

		$mediaString = substr($line, 1, -1);
		$mediaObject = explode(":", $mediaString);

		$this->mediaObjects[$mediaKey] = $mediaObject;

		return $mediaKey;
	}

	public function replaceMediaKeys($content)
	{
		$result = $content;

		$searchKeys = array();
		$replaceValues = array();

		// Render each media object
		foreach($this->mediaObjects as $mediaKey => $mediaObject)
		{
			$searchKeys[] = $mediaKey;

			$mediaHtml = $this->renderMedia($mediaKey, $mediaObject);

			$replaceValues[] = $mediaHtml;
		}

		// Run search and replace as a batch
		$result = str_replace($searchKeys, $replaceValues, $content);

		return $result;
	}

	private function renderMedia($mediaKey, $mediaObject)
	{
		$mediaHtml = "[$mediaKey]";

		if(count($mediaObject) > 0)
		{
			$mediaInstruction = array_shift($mediaObject);
			if(isset($this->mediaRenderers[$mediaInstruction]))
			{
				$mediaRenderer = $this->mediaRenderers[$mediaInstruction];
				$mediaHtml = call_user_func_array($mediaRenderer, $mediaObject);
			}
			else
			{
				$mediaHtml = sprintf("No mediaInstruction '%s' found for '%s'", $mediaInstruction, $mediaKey);
			}
		}
		else
		{
			$mediaHtml = sprintf("Found empty mediaObject for '%s'", $mediaKey);
		}

		return $mediaHtml;
	}
}