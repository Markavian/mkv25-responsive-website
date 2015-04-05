<?php

class OpenSource
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Open Source');
		$view->eyecatch('Open Source', "A round up of mkv25.net's open source projects across the web.");
		$view->banner('open-source short');
		
		$view->addSingleColumn('The open source section is waiting for the editor to do a write up.');
		
		echo $view->render();
	}
}