<?php

class History
{
	public function render($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('History');
		$view->eyecatch('History', 'A brief history of time, webspace, and everything.');
		$view->banner('history');
		
		$view->addSingleColumn("Launched in 2002 on the back of Universal Shipyards- mkv25.net has grown and warped into a blossoming network of sites featuring all the things I've held as hobbies over the years. Here is a history of the site featuring its key moments up til now.");
		
		$this->addSection($view, '2014 October', "I decided it was time for a overhaul of the site to promote the indie game work, and change the site into a place where people could learn about, play, and buy the games that I've been working on.");
		$this->addSection($view, '2014 September', 'During September I made a large number of my projects Open Source via Github, as an on going way to promote the code I write and share that with the world. A list of all my projects can be found at <a href="https://github.com/Markavian">my github profile</a>.');
		$this->addSection($view, '2012 April', 'Took part in my first <a href="http://www.ludumdare.com/compo/">Ludum Dare Competition</a> and released my first entry Miniature Worlds, which ended up with over 50,000 downloads on the Google Play store. This started me on a path of releasing mini-games on a regular basis and built up my confidence with my tools and release processes. The rest of my Ludum Dare entries can be found from <a href="http://ludumdare.com/compo/author/markavian/">my profile on the Ludum Dare website</a>.');
		$this->addSection($view, '2009', array("Neuro design created and site relaunch. The scrapbook was moved from the front page and archived to its own section. Articles, Gallery, Portfolio, and Games added. The strength of my the new gallery software and the cleanliness of the new design have got me quite exicted about the prospects of the new site.", "Also, in a landmark decision for me, I've shunned IE6 to focus on CSS2 compatable browsers. Years of supporting IE6 have taken their toll on thousands of web-developers world wide; I think its time to put that browser to sleep."));
		$this->addSection($view, '2008', 'DFMA - Dwarf Fortress Map Archive took off in a big way. Thousands of visits a day and hundreds of maps and movies uploaded every month as the <a href="http://mkv25.net/dfma/recentactivity.php">stats pages</a> reveal.');
		$this->addSection($view, '2007 May', '<a href="http://mkv25.net/dfma/">The Dwarf Fortress Map Archive</a> (DFMA) was launched in support of the #bay12game classic Dwarf Fortress. ' . "Its launch heralded a new era in map-sharing and epic fortress building that would allow players to share unbelievable constructions of immense size with relative ease. Made possible by my FDF-Map Viewer software (written using Flash AS3), in partnership with ShadowLord's Map Compressor software, the site is truely innovative - if I may say so myself.");
		$this->addSection($view, '2006', '2006 was a quiet time, where I was very happy with the website, and so posted content and articles while restraining myself from making any changes to the design, or the layout. There were also side projects going on behind the scenes unrelated to web-development.');
		$this->addSection($view, '2005 December', 'The unify template was launched on both the main site and USy site. A design for The Fake Clan website was also made but never implemented. The new layout was much crisper and colourful and presented the existing content for both sites fantasticly.');
		$this->addSection($view, '2004 May', 'The main site went through a radical overhaul, removing the forum, and focusing plainly on the scrapbook articles with a visual splattering of icons gridded across the front page. This layout would stick right up until late 2009.');
		$this->addSection($view, '2004 March', "In an attempt to make the forum more used it was moved to the front page with popular items listed along the top. Having a separate forum for a disparate set of content is the wrong way to handle comments and feedback, as demonstrated by the suprising popularity of the USy comments system; note- this would soon become the staple format for blogging across the internet (and soon to be expanded by google wave). I really can't stand forums and the words \"never again\" spring to mind~ when you compare forums to social networking sites you can see there are now much better ways to link content and comments together.");
		$this->addSection($view, '2003 October', "The focus of the site was moved towards a scrapbook, with a supporting forum. The design looked awful and the forum was under-used, so was quickly scrapped.");
		$this->addSection($view, '2003 August', "The I-Bead subsite - an support forum for an mp3 player made in Korea was launched. Being the only English language support forum for the device the site gained rapid popularity which would continue until the forum dwindled in usefulness in May 2005.");
		$this->addSection($view, '2003 July', "A sophisicated content management system that cached (rendered) all its pages as HTML was implemented along with a new crayola-blue design. Friends were invited to write more articles, but only a handful of useful entries were published.");
		$this->addSection($view, '2003 January', "The site was redesigned to cater for a 800x fixed width layout, with a cleaner cut white and grey design. mkv25.net's logo themed in red and navy.");
		$this->addSection($view, '2002 October', 'The website <a href="http://tfc.mkv25.net/">The Fake Clan</a> - a Worms Armageddon (pc game) fansite for maps and team members was launched. The map archived featured a shopping cart and PHP Zipping system for downloading batches of maps from the site in a convenient download. All of the maps were designed by me and gained popularity amongst pro-ropers around the net- some of them I consider works of art.');
		$this->addSection($view, '2002 June', array('Site launch... 640x fixed with 3 column layout focusing on short articles and images from my friends.', 'News on the site: <i>mkv25.net was registered on the 23rd of May 2002, and it worked, and I was excitied. I thought "This site needs a design! A cool, new design: something neat and tidy, original and useable. This webspace is my gift for people to come visit, to browse at their hearts content, I best make it look good"...</i>'));
		$this->addSection($view, '2002 April', 'The website <a href="http://mkv25.net/USy/">Universal Shipyards</a>, built around Space Empires IV graphics and modding, which was previously hosted on Tripod and other places for the previous 3 years, was finally moved to my new server. The site was already handling thousands of visits a day which eventually dwindled as the game aged. USy was my first site, and a major hobby, leading me to learn programming, webdesign, and 3D graphics.');
		$this->addSection($view, 'Epilogue', "You can read more about the history of mkv25.net by searching for mkv25.net on the wayback when machine. mkv25.net has always been a showcase of sorts, its my way of collecting ideas and memes together and passing them onwards to my friends. May that continue for many years to come...");

		return $view->render();
	}
	
	function addSection($view, $title, $content)
	{
		if (is_array($content))
		{
			$content = implode("</p>\n<p>", $content);;
		}
	
		$view->addSingleColumn("<heading>$title</heading><p>$content</p>");
	}
}