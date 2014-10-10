<?php

class Blog
{
	public function __construct($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Blog');
		$view->eyecatch('Blog', 'A blog about game development, software, and technology.');
		$view->banner('blog');
		
		$view->addSingleColumn('
Title
=====
Blog content not wired up to database.

* Relate
* Relief
* Educate
* Support
* Create

A point of interest
-------------------
And above all, have fun.

### Fun

Playful things and support.

### Games

Things to do on cold days and *dark* ~~nights~~ days.

Visit http://mkv25.net/blog for more details, and check out [our store](http://mkv25.net/store)!

');
		
		$view->render();
	}
}