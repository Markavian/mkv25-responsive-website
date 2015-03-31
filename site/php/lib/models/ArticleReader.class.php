<?php

class ArticleReader
{
	var $sql;

	public function __construct()
	{
		$this->sql = Sql::getInstance();
	}

	public function getArticleByUrlName($urlName)
	{
		$article = $this->getContentByUrlName($urlName);

		return $article;
	}

	public function getArticles()
	{
		$articles = $this->getContentByCategory('article', 5);

		return $articles;
	}

	public function getManyArticles()
	{
		$articles = $this->getLatestContent();

		return $articles;
	}

	public function getAllArticles()
	{
		$articles = $this->getAllContent();

		return $articles;
	}

	public function getArticlesForReferences($primaryId, $refArray)
	{
		$articles = $this->getContentByReferences($primaryId, $refArray);

		return $articles;
	}

	function getLatestContent()
	{
		$contentItems = array();

		$limit = 15;
		$query = sprintf("SELECT * FROM `shw_content` ORDER BY postdate DESC LIMIT %d", $limit);
		$queryName = "getLatestContent";

		$contentItems = $this->getContentForQuery($query, $queryName);
	
		return $contentItems;
	}

	function getAllContent()
	{
		$contentItems = array();

		$query = sprintf("SELECT * FROM `shw_content` ORDER BY urlname ASC");
		$queryName = "allContent";

		$contentItems = $this->getContentForQuery($query, $queryName);

		return $contentItems;
	}

	function getContentByUrlName($urlName)
	{
		$content = false;

		if(ArticleWriter::checkIfArticleExists($urlName))
		{
			// Read from file system
			$content = ArticleWriter::readArticleFromFile($urlName);
		}
		else
		{
			// Go to database
			$limit = 1;
			$query = sprintf("SELECT * FROM `shw_content` WHERE urlname = '%s' ORDER BY postdate DESC LIMIT %d", $urlName, $limit);
			$queryName = "contentByUrlName:$urlName";

			$contentItems = $this->getContentForQuery($query, $queryName);
			if(count($contentItems) == 1)
			{
				$content = $contentItems[0];
			}
		}

		return $content;
	}

	function getContentByCategory($category)
	{
		$contentItems = array();

		$allowedCategories = array('flash', 'article', 'artwork', 'experimental', 'java', '');
		if(in_array($category, $allowedCategories))
		{
			$limit = 5;
			$query = sprintf("SELECT * FROM `shw_content` WHERE category = '%s' ORDER BY postdate DESC LIMIT %d", $category, $limit);
			$queryName = "contentByCategory:$category";

			$contentItems = $this->getContentForQuery($query, $queryName);
		}

		return $contentItems;
	}

	function getContentByReferences($primaryId, $refArray)
	{
		$contentItems = array();

		if(is_numeric($primaryId) && is_array($refArray))
		{
			$refMatchers = '';
			foreach($refArray as $key=>$ref)
			{
				if($refMatchers != '')
					$refMatchers .= ' OR ';

				if(is_numeric($ref))
				{
					$refMatchers .= sprintf("id = '%d'", $ref);
				}
				else if($ref)
				{
					$refMatchers .= sprintf("urlname = '%s'", $ref);
				}
			}

			if($refMatchers != '')
				$refMatchers .= ' OR ';

			$limit = 8;
			$query = sprintf("SELECT * FROM `shw_content` WHERE %s
				icon2 = %d
				OR icon3 = %d
				OR icon4 = %d
				OR icon5 = %d
				ORDER BY postdate DESC LIMIT %d",
				$refMatchers, $primaryId, $primaryId, $primaryId, $primaryId, $limit);
			$queryName = "getContentByReferences:$primaryId";

			//echo "<pre>$query</pre>";

			$contentItems = $this->getContentForQuery($query, $queryName);
		}

		return $contentItems;
	}

	function getContentForQuery($query, $queryName)
	{
		$contentItems = array();

		$this->sql->query($query, $queryName);

		if($this->sql->num_rows($queryName) > 0)
		{
			while($row = $this->sql->fetch($queryName))
			{
				$contentItems[] = Article::createFrom($row);
			}
		}

		return $contentItems;
	}
}