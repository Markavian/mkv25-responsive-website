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