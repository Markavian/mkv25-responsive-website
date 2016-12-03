<?php

class ArticleReader
{
	var $dao;

	public function __construct()
	{
		$fallbacks = Array(
			new ArticleFileSystemReader(),
			new ArticleDatabaseReader()
		);

		// Setup the DAO object using a proxy to access both the Database and the File System
		$this->dao = new ProxyFallback($fallbacks);
	}

	public function getArticleByUrlName($urlName)
	{
		$article = $this->dao->getContentByUrlName($urlName);

		return $article;
	}

	public function getArticles()
	{
		$articles = $this->dao->getContentByCategory('article', 5);

		return $articles;
	}

	public function getManyArticles()
	{
		$articles = $this->dao->getLatestContent();

		return $articles;
	}

	public function getAllArticles()
	{
		$articles = $this->dao->getAllContent();

		return $articles;
	}

	public function getArticlesForReferences($primaryId, $refArray)
	{
		$articles = $this->dao->getContentByReferences($primaryId, $refArray);

		return $articles;
	}
}
