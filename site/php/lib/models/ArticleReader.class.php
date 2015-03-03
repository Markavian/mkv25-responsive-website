<?php

class ArticleReader
{
	var $sql;

	public function __construct()
	{
		$this->sql = Sql::getInstance();
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

	function getLatestContent()
	{
		$contentItems = array();

		$limit = 15;
		$query = sprintf("SELECT * FROM `shw_content` ORDER BY postdate DESC LIMIT %d", $limit);
		$queryName = "getLatestContent";

		$contentItems = $this->getContentForQuery($query, $queryName);
	
		return $contentItems;
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