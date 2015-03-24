<?php

class Scrapbook
{
	public function __construct($request)
	{
		// Get article from database
		$reader = new ArticleReader();
		$article = $reader->getArticleByUrlName($request->page);

		if($article) 
		{
			$linkedArticles = $reader->getArticlesForIdArray($article->linkedArticles);

			$view = new TemplateView();
			$view->baseUrl($request->base);
			$view->title($article->name);
			$view->eyecatch($article->name, $article->keywords);
			$view->banner('scrapbook short');

			$content = $article->renderFullArticle();
			$view->addSingleHTMLColumn($content);

			$linkedContent = Article::renderLinks($linkedArticles);
			$view->addSingleHTMLColumn($linkedContent);
			$view->render();
		}
		else
		{
			$view = new DefaultView();
			$view->responseCode(404, 'Article not found');
			$view->routeInfo();
		}
	}
}