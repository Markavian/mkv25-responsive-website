<?php

class ArticleFileSystemReader
{
	public function getLatestContent()
	{
		$contentItems = array();
		
		return $contentItems;
	}

	public function getAllContent()
	{
		$contentItems = array();
		
		return $contentItems;
	}

	public function getContentByUrlName($urlName)
	{
		$content = false;

		if(ArticleIO::checkIfArticleExists($urlName))
		{
			// Read from file system
			$content = ArticleIO::readArticleFromFile($urlName);
		}
		
		return $content;
	}

	public function getContentByCategory($category)
	{
		$contentItems = array();
		
		return $contentItems;
	}

	public function getContentByReferences($primaryId, $refArray)
	{
		$contentItems = array();

		if(is_numeric($primaryId) && is_array($refArray))
		{
			
		}

		return $contentItems;
	}
}