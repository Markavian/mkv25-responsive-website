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
			$view = new TemplateView();
			$view->baseUrl($request->base);
			$view->title($article->name);
			$view->eyecatch($article->name, $article->keywords);
			$view->banner('scrapbook short');

			$content = $article->renderFullArticle();
			$view->addArticle($content);
		}

		$view->render();
	}
}