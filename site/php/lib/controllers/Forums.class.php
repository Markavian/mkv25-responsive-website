<?php

class Forums
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Forums');
		$view->eyecatch('Forums', "A place to get involved, make suggestions, and get support.");
		$view->banner('forums short');
		
		$view->addSingleColumn('Forums are not yet open, come back soon!');
		$view->addSingleColumn('If you need to get in touch urgently, email at <a href="mailto:games@mkv25.net" >games@mkv25.net</a> or send a tweet to <a href="https://twitter.com/Markavian">@Markavian</a>.');
		
		echo $view->render();
	}
}