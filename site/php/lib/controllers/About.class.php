<?php

class About
{
	public function render($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('About');
		$view->eyecatch('About', 'Who, where, and why.');
		$view->banner('about');
		
		$view->addSingleColumn("mkv25.net is the personal and professional site of John Beech, my alter ego Markavian, and the site's mascot Fate. Websites have been built on this domain since 2002, and to this day the site continues to promote innovations, digital artwork, and games.");
		$view->addTripleColumns(
			'<img src="site/images/about_markavian.png"/> <center>Lucy Markavian - Family Maid</center>',
			'<img src="site/images/about_johnbeech.png"/> <center>John Beech - Site Editor</center>',
			'<img src="site/images/about_fate.jpg"/> <center>Fate - Friend and Mascot</center>'
		);
		$view->addDoubleColumns(
			'<heading>Where</heading><p>We are based out of Manchester, UK.</p><p>The site is hosted from a server in the USA to best serve traffic needs.</p>',
			'<heading>Why</heading><p>This site exists to promote games development, software development, digital artwork, and games communities - such as the <a href="//mkv25.net/dfma/">Dwarf Fortress Map Archive</a>, <a href="//mkv25.net/USy/">Universal Shipyards</a>, and <a href="//tfc.mkv25.net/">The Fake Clan</a>.</p>'
		);
		$view->addSingleColumn('<heading>Contact</heading><p>If you need to get in touch, send an email to <a href="mailto:johnbeech@mkv25.net">johnbeech@mkv25.net</a> or send a tweet to <a href="https://twitter.com/Markavian">@Markavian</a>.</p>');
		
		$view->addSingleColumn('For more information about mkv25.net check out the <a href="history">history</a> page.');
		
		return $view->render();
	}
}