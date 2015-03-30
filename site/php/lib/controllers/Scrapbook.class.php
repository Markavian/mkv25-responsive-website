<?php

class Scrapbook
{
	var $reader;

	public function __construct($request)
	{
		$this->reader = new ArticleReader();

		if($request->page == 'scrapbook')
		{
			$this->renderArticleList();
		}
		else
		{
			// Get article from database
			$article = $this->reader->getArticleByUrlName($request->page);

			if($article)
			{
				$this->renderArticle($article, $request);
			}
			else
			{
				$this->render404Response();
			}
		}
	}

	private function renderArticle($article, $request)
	{
		$linkedArticles = $this->reader->getArticlesForIds($article->id, $article->linkedArticles);

		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title($article->name);
		$view->eyecatch($article->name, $article->keywords);
		$view->banner('scrapbook short');

		$content = $article->renderFullArticle();
		$view->addSingleHTMLColumn($content);

		if(count($linkedArticles))
		{
			$linkedContent = ArticleFormatter::renderLinks($linkedArticles);
			$view->addSingleHTMLColumn($linkedContent);
		}

		$view->render();

		ArticleWriter::writeArticleToFile($article);
	}

	private function renderArticleList()
	{
		$view = new DefaultView();
		$view->responseCode(200, 'List of articles');

		// Get articles from database
		$articles = $this->reader->getAllArticles();

		// Save articles as physical files
		echo ArticleWriter::writeArticlesToFileSystem($articles);

		// Display an index of links
		$this->renderLinksForArticles($articles);
	}

	private function renderLinksForArticles($articles)
	{
		global $basePath;

		foreach ($articles as $index => $article)
		{
			$fileName = ArticleWriter::getFileNameFor($article->urlname);
			$articleFileInfo = ArticleWriter::checkIfArticleExists($article->urlname) ? $fileName : '';
			$articleUrl = $basePath . 'scrapbook/' . $article->urlname;

			echo <<<END
			<p><a href="$articleUrl">$article->name</a> - $articleFileInfo</p>
END;
		}
	}

	private function render404Response($article)
	{
		$view = new DefaultView();
		$view->responseCode(404, 'Article not found: ' . $request->page);
		$view->routeInfo();
	}
}