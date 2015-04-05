<?php

class ArticleFileSystemReader
{
	public function getLatestContent()
	{
		$contentItems = array();
		
		$limit = 10;
		$articles = $this->getAllContent();
		usort($articles, array('ArticleFileSystemReader', 'sortByDate'));
		
		$i = 0;
		foreach($articles as $article)
		{
			if ($i < $limit)
			{
				$contentItems[] = $article;
				$i++;
			}
		}
			
		return $contentItems;
	}
	
	private static function sortByDate($a, $b)
	{
		$dateA = strtotime($a->postdate);
		$dateB = strtotime($b->postdate);
	
		if ($a == $b) {
			return 0;
		}
		return ($a > $b) ? -1 : 1;
	}

	public function getAllContent()
	{
		return ArticleIO::readAllArticles();
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
		
		$limit = 10;
		$articles = $this->getAllContent();
		usort($articles, array('ArticleFileSystemReader', 'sortByDate'));
		
		foreach($articles as $article)
		{
			if ($article->category == $category)
			{
				$contentItems[] = $article;
			}
		}
			
		return $contentItems;
	}

	public function getContentByReferences($primaryId, $refArray)
	{
		$contentItems = array();

		if(is_numeric($primaryId) && is_array($refArray))
		{
			$articles = $this->getAllContent();
			
			$primaryRef = Article::$ID_INDEX[$primaryId]->urlname;
			
			if (is_array($articles))
			{
				foreach($articles as $key => $article)
				{
					// Include the article if it matches as a forward ref
					foreach($refArray as $ref)
					{
						if ($article->urlname == $ref)
						{
							$contentItems[$article->urlname] = $article;
						}
					}
					
					// Include the article if it matches as a backwards ref
					foreach($article->linkedArticles as $ref)
					{
						if ($ref == $primaryRef)
						{
							$contentItems[$article->urlname] = $article;
						}
					}
				}
			}
		}

		return $contentItems;
	}
}