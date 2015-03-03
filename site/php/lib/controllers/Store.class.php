<?php

class Store
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Store');
		$view->eyecatch('Store', "Buy full versions of our games here!");
		$view->banner('store short');
		
		$view->addSingleColumn('The store is not yet open, come back soon!');
		$view->addSingleColumn('If you need to get in touch urgently, email at <a href="mailto:games@mkv25.net" >games@mkv25.net</a> or send a tweet to <a href="https://twitter.com/Markavian">@Markavian</a>.');
		
		$view->render();
	}
}