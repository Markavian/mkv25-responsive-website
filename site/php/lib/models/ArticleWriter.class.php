<?php

class ArticleWriter
{
	public static function writeArticlesToFileSystem($articles)
	{
		$SITE_ROOT_URL = Environment::get('SITE_ROOT_URL');

		ob_start();

		foreach ($articles as $index => $article)
		{
			if(ArticleWriter::checkIfArticleExists($article->urlname) == false)
			{
				ArticleWriter::writeArticleToFile($article);

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
			$storagePath = ArticleWriter::getFilePathFor($article->urlname);
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

		if(ArticleWriter::checkIfArticleExists($articleUrlName))
		{
			$filePath = ArticleWriter::getFilePathFor($articleUrlName);

			$articleXML = simplexml_load_file($filePath);
			$article = Article::createFromXHTML($articleXML);
		}

		return $article;
	}

	public static function checkIfArticleExists($articleUrlName)
	{
		$filePath = ArticleWriter::getFilePathFor($articleUrlName);

		return file_exists($filePath);
	}

	public static function getFilePathFor($articleUrlName)
	{
		$ARTICLE_CONTENT_DIRECTORY = Environment::get('ARTICLE_CONTENT_DIRECTORY');

		$fileName = ArticleWriter::getFileNameFor($articleUrlName);

		return __DIR__ . '/../../' . $ARTICLE_CONTENT_DIRECTORY . '/' . $fileName;
	}

	public static function getFileNameFor($articleUrlName)
	{
		$sanitizedName = preg_replace("/[^A-Za-z0-9\_\-]/", '', $articleUrlName);
		$sanitizedName = strtolower($sanitizedName);
		$fileName = sprintf("%s.article.xml", $sanitizedName);

		return $fileName;
	}
}