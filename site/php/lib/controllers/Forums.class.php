<?php

class Forums
{
	public function render($request)
	{
		if(!$request->page)
		{
			header('Location: index');
		}
		else if($request->page == 'forums')
		{
			header('Location: forums/index');
		}
		else if($request->page == 'index')
		{
			$view = new TemplateView();
			$view->baseUrl($request->base);
			$view->title('Forums');
			$view->eyecatch('Forums', "A place to get involved, make suggestions, and get support.");
			$view->banner('forums short');

			$view->addSingleColumn('Forums are not yet open, come back soon!');

			$forumIndex = new Template('forum/forum-index.template.html');
			$view->addSingleColumn($forumIndex->expand());
			$view->addSingleColumn('If you need to get in touch urgently, email at <a href="mailto:games@mkv25.net" >games@mkv25.net</a> or send a tweet to <a href="https://twitter.com/Markavian">@Markavian</a>.');

			return $view->render();
		}
		else if($request->folder == 'forums/topic/')
		{
			$topicId = $request->page;
			$topicName = ucfirst($topicId);
			$topicName = str_replace('-', ' ', $topicName);

			$view = new TemplateView();
			$view->baseUrl($request->base);
			$view->title($topicName);
			$view->eyecatch($topicName, "");
			$view->banner('forums short');

			$breadcrumbs = array(
				'Forums' => 'forums/index',
				'Topic' => '',
				$topicName => 'forums/topic/' . $topicId
			);

			$breadcrumbTrail = BreadcrumbFormatter::renderBreadcrumbTrail($breadcrumbs);

			$view->addSingleColumn($breadcrumbTrail);
			$view->addSingleColumn('Topic name: ' . $topicName);

			return $view->render();
		}
		else
		{
			$view = new DefaultView();
			$view->responseCode(404, 'Forum page not found');
			$view->routeInfo();

			return $view->render();
		}
	}
}
