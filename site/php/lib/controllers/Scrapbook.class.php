<?php

class Scrapbook
{
	public function __construct($request)
	{

		if($request->page == 'scrapbook')
		{
			$this->renderArticleList();
		}
		else
		{
			// Get article from database
			$reader = new ArticleReader();
			$article = $reader->getArticleByUrlName($request->page);

			if($article)
			{
				$this->renderArticle($article);
			}
			else
			{
				$this->render404Response();
			}
		}
	}

	private function renderArticle($article)
	{
		$linkedArticles = $reader->getArticlesForIds($article->id, $article->linkedArticles);

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
		$view = new DefaultView();
		$view->responseCode(200, 'List of articles');

		// Get articles from database
		$articleReader = new ArticleReader();
		$articles = $articleReader->getManyArticles();

		foreach ($articles as $index => $article)
		{
			$xhtml = $article->toXHTML();
			$xhtml = htmlspecialchars($xhtml);
			
			echo "<p>$article->name</p>";
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