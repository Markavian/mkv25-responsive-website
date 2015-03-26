/* Navigation toggles for responsive design */

var navicon, navigation, links, banner;

function getFirstTag(search, name)
{
	return search.getElementsByTagName(name)[0];
}

function toggleNavigation(event)
{
	if(links.className == "")
	{
		links.className = "collapsed";
	}
	else
	{
		links.className = "";
	}
}

function hideNavigation(event)
{
	links.className = "collapsed";
	document.removeEventListener("click", hideNavigation);
}

function registerNavigationToggle()
{
	navicon = getFirstTag(document, "navicon");
	navigation = getFirstTag(document, "navigation");
	links = getFirstTag(navigation, "links");
	banner = getFirstTag(document, "banner");

	navicon.addEventListener("click", toggleNavigation);
}
