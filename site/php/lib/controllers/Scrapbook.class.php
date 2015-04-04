<?php

class Scrapbook
{
	var $reader;

	public function __construct($request)
	{
		$this->reader = new ArticleReader();

		if($request->page == 'scrapbook' || !$request->page)
		{
			$this->renderArticleList($request);
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
				$this->render404Response($request);
			}
		}
	}

	private function renderArticle($article, $request)
	{
		$linkedArticles = $this->reader->getArticlesForReferences($article->id, $article->linkedArticles);

		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title($article->name);
		$view->eyecatch($article->name, $article->keywords);
		$view->banner('scrapbook short');

		$content = $article->renderFullArticle();
		$view->addSingleColumn($content);

		if(count($linkedArticles))
		{
			$linkedContent = "<heading>Related</heading>";
			$linkedContent .= ArticleFormatter::renderLinksAsIcons($linkedArticles);

			$view->addSingleColumn($linkedContent);
		}

		$view->render();
	}

	private function renderArticleList($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title("Scrapbook");
		$view->eyecatch("Scrapbook", "scrapbook html javascript flash artwork index");
		$view->banner('scrapbook short');

		// Get articles from database
		$articles = $this->reader->getAllArticles();

		// Save articles as physical files
		if ($articles)
		{
			$newFiles = ArticleIO::writeArticlesToFileSystem($articles);
			if($newFiles)
			{
				$view->addSingleColumn($newFiles);
			}

			$iconList = ArticleFormatter::renderLinksAsIcons($articles);
			$view->addSingleColumn($iconList);
		}
		else
		{
			$view->addSingleColumn("No articles found.");
		}

		$view->render();
	}

	private function render404Response($request)
	{
		$view = new DefaultView();
		$view->responseCode(404, 'Article not found: ' . $request->page);
		$view->routeInfo();
	}
}