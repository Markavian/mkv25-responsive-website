<?php

class ArticleIO
{
	static $FILE_INDEX = array();

	public static function writeArticlesToFileSystem($articles)
	{
		$SITE_ROOT_URL = Environment::get('SITE_ROOT_URL');

		ob_start();

		foreach ($articles as $index => $article)
		{
			if(ArticleIO::checkIfArticleExists($article->urlname) == false)
			{
				ArticleIO::writeArticleToFile($article);

				$articleUrl = $SITE_ROOT_URL . 'scrapbook/' . $article->urlname;

				echo <<<END
			<p>Saved article as file: <a href="$articleUrl">$article->name</a></p>
END;
			}
		}

		return ob_get_clean();
	}

	public static function writeArticleToFile($article)
	{
		if(Article::isValidArticle($article))
		{
			$storagePath = ArticleIO::getFilePathFor($article->urlname);
			$contentToWrite = $article->toXHTML();
			$contentToWrite = mb_convert_encoding($contentToWrite, "UTF-8");

			$file = fopen($storagePath, "w");
			fwrite($file, $contentToWrite);
			fclose($file);
		}
	}

	public static function readArticleFromFile($articleUrlName)
	{
		$article = false;

		if(ArticleIO::checkIfArticleExists($articleUrlName))
		{
			$filePath = ArticleIO::getFilePathFor($articleUrlName);

			$articleXML = simplexml_load_file($filePath);
			$article = Article::createFromXHTML($articleXML);
		}

		return $article;
	}

	public static function readAllArticles()
	{
		$articles = Array();

		$directoryPath = ArticleIO::getDirectoryPath();

		$fileList = scandir($directoryPath);

		foreach($fileList as $key => $fileName)
		{
			if (endsWith($fileName, 'article.xml'))
			{
				if (isset(ArticleIO::$FILE_INDEX[$fileName]))
				{
					$article = ArticleIO::$FILE_INDEX[$fileName];
				}
				else
				{
					$filePath = $directoryPath . $fileName;
					$articleXML = simplexml_load_file($filePath);
					$article = Article::createFromXHTML($articleXML);

					ArticleIO::$FILE_INDEX[$fileName] = $article;
				}

				if ($article)
				{
					$articles[] = $article;
				}
			}
		}

		return $articles;
	}

	public static function checkIfArticleExists($articleUrlName)
	{
		$filePath = ArticleIO::getFilePathFor($articleUrlName);

		return file_exists($filePath);
	}

	private static function getFilePathFor($articleUrlName)
	{
		$fileName = ArticleIO::getFileNameFor($articleUrlName);
		$directoryPath = ArticleIO::getDirectoryPath();

		return $directoryPath . $fileName;
	}

	private static function getDirectoryPath()
	{
		$ARTICLE_CONTENT_DIRECTORY = Environment::get('ARTICLE_CONTENT_DIRECTORY');

		return __DIR__ . '/../../../' . $ARTICLE_CONTENT_DIRECTORY . '/';
	}

	public static function getFileNameFor($articleUrlName)
	{
		$sanitizedName = preg_replace("/[^A-Za-z0-9\_\-]/", '', $articleUrlName);
		$sanitizedName = strtolower($sanitizedName);
		$fileName = sprintf("%s.article.xml", $sanitizedName);

		return $fileName;
	}
}
