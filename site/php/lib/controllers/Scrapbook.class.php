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
	}

	private function renderArticleList()
	{
		global $basePath;

		$view = new DefaultView();
		$view->responseCode(200, 'List of articles');

		// Get articles from database
		$articles = $this->reader->getAllArticles();

		foreach ($articles as $index => $article)
		{
			$articleUrl = $basePath . 'scrapbook/' . $article->urlname;
			echo <<<END
			<p><a href="$articleUrl">$article->name</a></p>
END;
		}

		// $this->exportArticlesAsXHTML($articles);
	}

	private function exportArticlesAsXHTML($articles)
	{
		foreach ($articles as $index => $article)
		{
			$xhtml = $article->toXHTML();
			$xhtml = htmlspecialchars($xhtml);

			echo "<pre>$xhtml</pre>";
		}
	}

	private function render404Response($article)
	{
		$view = new DefaultView();
		$view->responseCode(404, 'Article not found: ' . $request->page);
		$view->routeInfo();
	}
}