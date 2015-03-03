<?php

class BBCodeOutput 
{
	public static function process($string, $simpleHTML=false)
	{
		$tagsToIgnore = array();
		$tagsToRemove = ($simpleHTML) ? array('img', 'flash') : array();
		$parser = new UbbAdminParser($tagsToIgnore, $tagsToRemove);
		

		$string = $parser->parse($string);
		$string = BBCodeOutput::nl2para($string);
		$string = BBCodeOutput::removePTagsfromPRE($string);
		$string = BBCodeOutput::fixParaTags($string);
		return $string;
	}

	/* Functions from mkv16 */
	public static function prepareText($string) {
		return addslashes(htmlspecialchars(stripslashes(strip_tags($string)), ENT_QUOTES));
	}

	public static function cleanUserData($string) {
		return htmlspecialchars(strip_tags($string), ENT_QUOTES); 
	}

	public static function unprepareText($string) {
		return stripslashes(htmlspecialchars_decode($string, ENT_QUOTES));
	}

	public static function removePTagsfromPRE($string)
	{
		// Initialise internal variables
		$stripBR = false;
		$output = "";
		$search = array('<p>', '</p>');

		// Break content into array based on <pre> and </pre> tags
		$content = preg_split("/(<pre|<\/pre>)/", $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		
		// Loop through content array
		for($i=0; $i<count($content); $i++) {
			if($content[$i] == '<pre') {
				// Start stripping if <pre> detected
				$stripBR = true;
				$output .= $content[$i];
			} else
			if($content[$i] == '</pre>') {
				// Stop stripping if </pre> detected
				$stripBR = false;
				$output .= $content[$i];
			} else {
				// Strip or not
				if($stripBR) {
					$output .= str_replace($search, '', $content[$i]);
				} else {
					$output .= $content[$i];
				}
			}
		}
		
		// Return compiled string
		return $output;
	}

	public static function removeBRfromPRE($string)
	{
		// Initialise internal variables
		$stripBR = false;
		$output = "";

		// Break content into array based on <pre> and </pre> tags
		$content = preg_split("/(<pre|<\/pre>)/", $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		
		// Loop through content array
		for($i=0; $i<count($content); $i++) {
			if($content[$i] == '<pre') {
				// Start stripping if <pre> detected
				$stripBR = true;
				$output .= $content[$i];
			} else
			if($content[$i] == '</pre>') {
				// Stop stripping if </pre> detected
				$stripBR = false;
				$output .= $content[$i];
			} else {
				// Strip or not
				if($stripBR) {
					$output .= str_replace("<br />", "", $content[$i]);
				} else {
					$output .= $content[$i];
				}
			}
		}
		
		// Return compiled string
		return $output;
	}

	public static function nl2para($content)
	{
		$NL = "\n";
		$lines = explode($NL, $content);
		$output = array();
		if (count($lines)) 
		{ 
			for ($i = 0; $i < count($lines); $i++)
			{
				$line = $lines[$i];
				$line = str_replace('><br />', '><p>', $line);
				$line = str_replace('<br />', '', $line);
				$line = trim($line);
				if($line) 
					$output[] = '<p>' . $line . '</p>'; 
			}
			$content = implode($NL, $output); 
		}
		return $content;
	}

	public static function fixParaTags($content)
	{
		return str_replace(array('<p><', '></p>'), array('<', '>'), $content);
	}

	public static function bbCodeStrip($string) {
		$find = array( 
			"'\[b\](.*?)\[/b\]'is", 
			"'\[link\](.*?)\[/link\]'i", 
			"'\[link=(.*?)\](.*?)\[/link\]'i",
			"'\[url\](.*?)\[/url\]'i", 
			"'\[url=(.*?)\](.*?)\[/url\]'i",
			"'\[i\](.*?)\[/i\]'is",
			"'\[poi=(.*?)\](.*?)\[/poi\]'i",
			"'\[map=(.*?)\](.*?)\[/map\]'i",
			"'\[movie=(.*?)\](.*?)\[/movie\]'i",
			"'\[code\](.*?)\[/code\]'is",
			"'\[sidenote\+rant\](.*?)\[/sidenote\+rant\]'is",
			"'\[rant\](.*?)\[/rant\]'is",
			"'\[img=(.*?)\](.*?)\[/img\]'i",
			"'\[img\](.*?)\[/img\]'i",
			"'\[wiki=(.*?)\](.*?)\[/wiki\]'i",
			"'\[h(1|2|3|4)\](.*?)\[/h(1|2|3|4)\]'i",
			"'\[flash\](.*?)\[/flash\]'i",
		); 
		
		$replace = array( 
			"\\1", 
			"\\1", 
			"\\2",
			"\\1", 
			"\\2",
			"\\1",
			"\\2",
			"\\2",
			"\\2",
			"\\1",
			"\\1",
			"\\1",
			"\\2",
			"\\1",
			"\\2",
			"\\2",
			"" 		
		); 

		$output = preg_replace($find, $replace, $string); 
		
		return $output;
	}

	/* Taken from bob at 808medien dot de Posted on php.net/substr on 16-Sep-2005 09:10 */
	public static function truncate_string ($string, $maxlength, $extension, $dropFirst=false)
	{
		// Set the replacement for the "string break" in the wordwrap function
		$cutmarker = "**cut_here**";

		// Checking if the given string is longer than $maxlength
		if (strlen($string) > $maxlength)
		{
			// Using wordwrap() to set the cutmarker
			// NOTE: wordwrap (PHP 4 >= 4.0.2, PHP 5)
			$string = wordwrap($string, $maxlength, $cutmarker);

			// Exploding the string at the cutmarker, set by wordwrap()
			$string = explode($cutmarker, $string);

			// Adding $extension to the first value of the array $string, returned by explode()
			$string = $string[0] . $extension;
		}
		 
		/* Added by Markavian to truncate strings that start in odd places */
		if($dropFirst) {
			$segments = explode(' ', $string, 2);
		$string = $segments[1];
		}

		// returning $string
		return $string;
	}

	public static function relativeDate($timestamp, $short=false)
	{
		$dayofyear = date("z", $timestamp);
		$weekofyear = date("W", $timestamp);
		$monthofyear = date("n", $timestamp);
		$year = date("Y", $timestamp);
		
		$todaysdayofyear = date("z");
		$todaysweekofyear = date("W");
		$todaysmonthofyear = date("n");
		$todaysyear = date("Y");
		
		$datef = date("Y-m-d", $timestamp);
		
		if($short)
		{
			$suffix = '';
		}
		else
		{
			$suffix = ', '.$datef;
		}
		
		if($year == $todaysyear)
		{
			$diff = time() - $timestamp;
			if($diff < 60)
			{
				return $diff.' seconds ago';
			}
			else if($diff < 3600)
			{
				$minutes = round($diff/60);
				if($minutes == 1)
				{
					return '1 minute ago';
				} 
				else
				{
					return $minutes . ' minutes ago';
				}
			}
			else if($diff < 172800)
			{
				$hours = round($diff/3600);
				if($hours == 1)
				{
					return '1 hour ago';
				}
				else
				{
					return $hours . ' hours ago';
				}
			}
			else if($dayofyear == $todaysdayofyear-2)
			{
				return 'Two days ago'.$suffix;
			}
			else
			{
				return date("d/m/Y h:m", $timestamp);
			} 
		}
		return $datef;
	}

	public static function safeURLTitle($str)
	{
		$title = str_replace(' ', '-', $str);
		$title = strtolower(preg_replace('#[^A-Za-z0-9_-]#','',unprepareText($title)));  
		$title = str_replace(array('039'), array(''), $title);
		return $title;
	}
}

// Pre PHP 5.0 compatability
if(!function_exists('stripos'))
{
   function stripos($haystack,$needle,$offset = 0)
   {
     return(strpos(strtolower($haystack),strtolower($needle),$offset));
   }
}

if (!function_exists('htmlspecialchars_decode')) {
	function htmlspecialchars_decode($str, $options=ENT_COMPAT) {
		$trans = get_html_translation_table(HTML_SPECIALCHARS, $options);
		
		$decode = ARRAY();
		foreach ($trans AS $char=>$entity) {
			$decode[$entity] = $char;
		}
		
		$str = strtr($str, $decode);
		
		return $str;
	}
} 
