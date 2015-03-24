<?php

class News
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('News');
		$view->eyecatch('News', 'A blog about game development, software, and technology.');
		$view->banner('blog short');

		// Get articles from database
		$reader = new ArticleReader();
		$articles = $reader->getManyArticles();

		foreach($articles as $key=>$article)
		{
			$content = $article->renderFullArticle();
			$view->addSingleHTMLColumn($content);
		}
		
		$view->render();
	}
}