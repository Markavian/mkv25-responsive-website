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

	public function getArticlesForIds($primaryId, $idArray)
	{
		$articles = $this->getContentByIDs($primaryId, $idArray);

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

	function getContentByUrlName($urlName)
	{
		$content = false;

		$limit = 1;
		$query = sprintf("SELECT * FROM `shw_content` WHERE urlname = '%s' ORDER BY postdate DESC LIMIT %d", $urlName, $limit);
		$queryName = "contentByUrlName:$urlName";

		$contentItems = $this->getContentForQuery($query, $queryName);
		if(count($contentItems) == 1)
		{
			$content = $contentItems[0];
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

	function getContentByIDs($primaryId, $idArray)
	{
		$contentItems = array();

		$allowedCategories = array('flash', 'article', 'artwork', 'experimental', 'java', '');
		if(count($idArray) > 0)
		{
			$idMatchers = '';
			foreach($idArray as $key=>$id)
			{
				if($idMatchers != '')
					$idMatchers .= ' OR ';

				$idMatchers .= sprintf("id = '%d'", $id);
			}
			$limit = 5;
			$query = sprintf("SELECT * FROM `shw_content` WHERE %s
				OR icon2 = %d
				OR icon3 = %d
				OR icon4 = %d
				OR icon5 = %d
				ORDER BY postdate DESC LIMIT %d",
				$idMatchers, $primaryId, $primaryId, $primaryId, $primaryId, $limit);
			$queryName = "getContentByIDs:" . implode($idArray);

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