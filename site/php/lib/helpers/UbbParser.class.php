<?php
  /**********************************************
  * quickerUbb (c)2004 Roönaän
  *          
  * version 1.4:
  * [25/02/2004]:
  * Added a _quickerUBB_isTextTag() method in order to
  * support tags like [php] en [code] whom's inner code
  * should be skipped while parsing.
  * In order to add tags, you should edit this method,
  * Starting at line 119 of this file.
  *
  * 
	*
  * [14/02/2004]: Fixed the problem with [php] tags where
  * for all array-indexes an closing tag was added.:
  * ie: [php]$a[0] = 0;[/php] resulted in
  * <? $a[0] = 0; [/0]?>
  *
  * [12/08/2003]: Fixed the empty string/infinit loop bug
  * [09/08/2003]: Added security-check to url/mail/img
  * [04/08/2003]: Fixed lowercase tags only bug
  *
  * Ubb Parsing Engine based on stacks.
  *
  * Add additional parse_ubbtag methods to the main class.
  * For adding smiles and for applying htmlspecialchars and
  * transformation of \n\r to <br /> edit the ubbtexthandler
  * method
  *
  * In this file some example stylesheets are used, as a
  * additional example parsing in case this file is called
  * upon directly and not bij inclusion.
  *
  * For Questions and comments: hotscripts@roonaan.nl
  * please add script name to your email subject.
  */
  
  $start_time = !isset($start_time) ? explode(' ', microtime()) : $start_time;

  /**
  * UBB_LOOKDOWN defines the number of elements the parser
  * descends to find a matching closing element
  */
  define('UBB_LOOKDOWN', 2);
  
  /**
  * UBB_IMG_MAX_RESIZE_WIDTH and UBB_IMG_MAX_RESIZE_HEIGHT
  * define the highest values which can be used in the img
  * tags: [img=height, width]url[/img]
  * and [img=width]ur;[/img]
  **/
  define('UBB_IMG_MAX_RESIZE_WIDTH', 600);
  define('UBB_IMG_MAX_RESIZE_HEIGHT', 1000);

/* UBB Text handler allows modification of text in order
   to apply additional ubb tags, or smiles */
function ubbtexthandler($text, $ubbObj = null)
{
  $text = htmlspecialchars($text);
  if(is_object($ubbObj)) if(strpos(strtolower($text), '/me ') > 0) $text = eregi_replace("([^[])/me ([^\n\r$]*)([\n\r$])", "\\1<span class=\"me\">*".$ubbObj->username." \\2 *</span>\\3", $text); 
  $text = nl2br($text);
  
  //echo '<div>'.htmlspecialchars($text).'</div>';
  $smiles = array();
  $smiles['&lt;:)&gt;'] = 'beard';
  $smiles['&gt;:)'] = 'Evil';
  $smiles[':)'] = 'Smile';
  $smiles['|:('] = 'Headbanger';
  $smiles[':('] = 'Angry';
  $smiles[':\'('] = 'Rears';
  $smiles[':o'] = 'Amazed';
  $smiles[':D'] = 'Big Smile';
  $smiles[':r'] = 'Disgusted';
  $smiles[':9~'] = 'Jummy!';
  $smiles[':9'] = 'Delicious';
  $smiles[';)'] = 'Wink';
  $smiles[':9'] = 'Delicious';
  $smiles[':7'] = 'Love It';
  $smiles[':+'] = 'Clown';
  $smiles['O+'] = 'Heart';
  $smiles[':*'] = 'Kiss';
  $smiles['}:O'] = 'Stupid Cow';
  $smiles['^)'] = 'Married';
  $smiles['_O_'] = 'Worshippie';
  $smiles[':W'] = 'Wave goodbye';
  $smiles['^O^'] = 'Way To Go!';
  $smiles[':?'] = 'Come Again?';
  $smiles['(8&gt;'] = 'Spy vs. Spy';
  $smiles[':Y)'] = 'Vork';
  $smiles[':Z'] = 'Sleeping';
  $smiles[';('] = 'cry';
  $smiles['}:|'] = 'Grmbl';
  $smiles[':z'] = 'Sleepy';
  $smiles['}&gt;'] = 'Evil';
  $smiles[':X'] = 'Hgnn';
  $smiles[':O'] = 'Booooring';
  $smiles['*)'] = 'Prodent';
  $smiles[':{'] = 'Uhuh';
  $smiles['O-)'] = 'The Saint';
  $smiles['8-)'] = 'Sunchaser';
  $smiles['*;'] = 'Liefde is';
  $smiles[':Y'] = 'Yes';
  $smiles[':N'] = 'No';
  $smiles[':@'] = 'Ashamed';
  $smiles['8)7'] = 'Twisted';
  $smiles[':P'] = 'puh';
  foreach($smiles as $ubb => $html)
    $text = str_replace($ubb, '<small>&lt;<b>'.$html.'</b>&gt;</small>', $text);
  
  return $text;
}

/** In order to handle tags like [php] or [code] of which the inner
 *  text should not be parsed, a new method is created, stated below
 *
 *  It implements a in_array statements.
 *
 *  Be aware! : All listed tags should be lowercase in order to be
 *  recognized correctly.
 */
function _quickerUBB_isTextTag($tag)
{
  return in_array($tag,
  array(

  'code',
  'php',
  ));
}

/********
* ubbParsing class.
*
* This class builds an tree of stackItems objects and from
* there derives an appropriate html structure based upon
* code generation methods. Each code generation method
* parse_[ubb], as where [ubb] is an ubb tag which is
* supported by the parser. After adding an additional
* method, the parser will recognize the code generation
* method and apply this method when encountering a matching
* ubb-tag while parsing.
*
* In order to use the parser, initialize an UbbParser object
* and call the following method
* 
* $initializedUbbParser->parse($ubb)
*
* This class can be a superclass for more flexible classess,
* for instanse the UbbAdminParser which is used to parse
* site admin messages and which allowes html input, using the
* [html]html code[/html] tag.
*
* When using the /me tag (which will automatically be
* replace to a [me=username][/me] structure), you should use
* $parser->setUsername('username') first.
*/

class UbbParser
{
  var $usedTags;
  var $username;
	var $siteroot;
	var $tagsToIgnore;
	var $tagsToRemove;
  
  function setUsername($username)
  {
    $this->username = eregi_replace('([^a-z0-9_~]*)', '', $username);
  }
	
	function setSiteroot($url)
	{
		$this->siteroot = $url;
	}
  
  function UbbParser($tagsToIgnore=array(), $tagsToRemove=array())
  {
    $this->usedTags = array();
    $this->textTags = array();
		$this->tagsToIgnore = $tagsToIgnore;
		$this->tagsToRemove = $tagsToRemove;
    $this->username = '';
		$this->siteroot = '';
		
    $methods = get_class_methods(get_class($this));

    foreach($methods as $m)
    {
      if(substr($m, 0, 6) == 'parse_')
      {
        $tag = substr($m, 6);
				if(in_array($tag, $this->tagsToRemove))
				{
					$this->usedTags[$tag] = 'remove_tag';
				}
				else
				if(in_array($tag, $this->tagsToIgnore))
				{
        	$this->usedTags[$tag] = 'ignore_tag';
				}
				else
				{
        	$this->usedTags[$tag] = $m;
				}
      }
			else
			if($m == 'general_url_parse')
			{
				$this->usedTags[$m] = $m;
			}
    }  
  }

  function parse($text)
  {
     if(strpos(strtolower($text), '/me') > 0) $text = preg_replace("/([^[])\/me([^\n\r$]*)([\n\r$])/i", "/\\1[me=$this->username]\\2[\/me]\\3/i", $text);
     $text = str_replace('[*]','[li]', $text);
     $text = str_replace('[/*]','[/li]', $text);
		 
		 /* Additional mkv25.net pre processing */
     $text = str_replace('[link','[url', $text);
     $text = str_replace('[/link]','[/url]', $text);
		 /*
		 $code = '{::}';
     $text = str_replace(array("\n", "\r"), array($code, ''), $text);
     $text = str_replace(']'.$code, ']', $text);
     $text = str_replace($code, "\n", $text);
		 */
		 
     $basetree = new stackItem();
     $basetree->build(' '.trim($text));
     $text = $basetree->parse($this, $this->usedTags);
		 
		 /* Additional mkv25.net post processing */
		 // None at this time
		 return $text;
  }

  /* Auxilary method which calls upon the ubbtexthandler
     method, or does noting when not found */
  function parse_text($tree)
  {
    $this->text_handler = 'ubbtexthandler';
    if(isset($this->text_handler))
    {
      if(function_exists($this->text_handler))
      {
        $f = $this->text_handler;
        return $f($tree, $this);
      }
    }
    return $text;
  }
  
  /* base function to convert a [*]text[*] to <**>text</**> */
  function simple_parse($tree, $html_pre, $html_post, $parseInner = true, $htmlspecialchars = true, $nl2br = true)
  {
    $text = $parseInner ? $tree->innerToHtml($this, $this->usedTags) : $tree->toText();
    $text = strlen($text) > 0 ? $html_pre.$text.$html_post : '';
    /* Added a $nl2br check, thanx to Bert Goedhals */
    if ( !$nl2br )
    {
      $text = str_replace ('<br />', '', $text); 
			$text = str_replace (array('<p>', '</p>'), '', $text); 
    }
    return $text;
  }
  
  /* code generation methods */
  function parse_hr($tree)   {return $this->simple_parse($tree, '<hr />', '');}
  function parse_br($tree)   {return $this->simple_parse($tree, '<br />', '');}
  function parse_i($tree)    {return $this->simple_parse($tree, '<i>', '</i>');}
  function parse_u($tree)    {return $this->simple_parse($tree, '<u>', '</u>');}
  function parse_s($tree)    {return $this->simple_parse($tree, '<s>', '</s>');}
  function parse_b($tree)    {return $this->simple_parse($tree, '<b>', '</b>');}
  function parse_sub($tree)  {return $this->simple_parse($tree, '<sub>', '</sub>');}
  function parse_sup($tree)  {return $this->simple_parse($tree, '<sup>', '</sup>');}
  function parse_code($tree) {return '<blockquote class="qbb code"><b>Code:</b><pre>' .$tree->toText() . '</pre></blockquote>';}
  function parse_php($tree)  {return '<blockquote class="qbb code"><b>Php:</b><pre>'.highlight_string('<?php '.$tree->toText().'?>', true).'</pre></blockquote>';}
  /* Methods parse_list/_ul__ol_li are updated to match XHtml thanx to Bert Goedhals */
  function parse_list($tree) {return $this->simple_parse($tree, '<ul>', '</ul>', true, true, false);}
  function parse_ul($tree)   {return $this->simple_parse($tree, '<ul>', '</ul>', true, true, false);}
  function parse_ol($tree)   {return $this->simple_parse($tree, '<ol>', '</ol>', true, true, false);}
  function parse_li($tree)   {return $this->simple_parse($tree, '<li>', '</li>', true, true, false);}
  function parse_edit($tree) {return $this->simple_parse($tree, '<span class="edit"><b>edit:</b>','</span>');}
  function parse_bold($tree) {return $this->simple_parse($tree, '<b>', '</b>');}
  function parse_quote($tree){return $this->simple_parse($tree, '<blockquote>', '</blockquote>');}

  /* more complex code generation methods */
  function parse_me($tree, $params = array())
  {
     $me = isset($params['me']) ? '*'.$params['me'].' ' : '*';
     return $this->simple_parse($tree, '<span class="me">'.$me, '*</span>');
  }
  function parse_url($tree, $params = array())
  {
     /* [url]href[/url] as well as [url=href]text[/url] is supported */
     $href = isset($params['url']) ? $params['url'] : $tree->toText();
     $href = $this->valid_url($href) ? $href : '';
     return $this->simple_parse($tree, '<a href="'.htmlspecialchars($href).'">', '</a>');
  }
  function parse_mail($tree, $params = array())
  {
     /* [mail]email[/mail] as well as [mail=email]text[/mail] is supported */
     $href = isset($params['mail']) ? $params['mail'] : $tree->toText();
     return $this->simple_parse($tree, '<a href="mailto:'.htmlspecialchars($href).'">', '</a>');
  }  
  function parse_img($tree)
  {
    $cw1 = '';
    $cw2 = '';
    $text = $tree->toText();
    $params = $tree->getParameters();
    $height = ''; $width = ''; $align = '';
    if(isset($params['img']))
    {
      $size = explode(',',trim($params['img']));
      $c = count($size);
      if($c==2)
      {
        $height = is_numeric($size[1]) ? (intval($size[1]) < UBB_IMG_MAX_RESIZE_HEIGHT) ? ' height="'.$size[1].'"' : '' : '';
        $width  = is_numeric($size[0]) ? (intval($size[0]) < UBB_IMG_MAX_RESIZE_WIDTH) ? ' width="'.$size[0].'"' : '' : '';
      }
      else if($c==1)
      {
        $width  = is_numeric($size[0]) ? (intval($size[0]) < UBB_IMG_MAX_RESIZE_WIDTH) ? ' width="'.$size[0].'"' : '' : '';
      }
    }
    if(isset($params['align']))
    {
      $s = strtolower($params['align']);
      if($s == 'left' || $s == 'links') $align = ' align="left"';
      if($s == 'right' || $s == 'rechts') $align = ' align="right"';
			if($s == 'center') { $cw1 = '<center>'; $cw2 = '</center>'; } else { $cw1 = ''; $cw2 = ''; }
    }
    $text = $this->valid_url($text) ? $text : '';
    return $cw1 . '<img'.$height.$width.$align.' src="'.htmlspecialchars($text).'" />' . $cw2;
  }
  function valid_url($href)
  {
     $lowhref = strtolower($href);
     return (
             (substr($lowhref,0,7)=='http://')
          || (substr($lowhref,0,8)=='https://')
          || (substr($lowhref,0,2)=='//')
          || (substr($lowhref,0,6)=='ftp://')
          || (substr($lowhref,0,7)=='mailto:')
        );
  }
	/* Additional custom codes for use on mkv25.net with neuro 21/10/2009 */
	function parse_link($tree, $params = array()) { return $this->parse_url($tree, $params); }
	function parse_h1($tree)    {return $this->simple_parse($tree, '<h1>', '</h1>');}
	function parse_h2($tree)    {return $this->simple_parse($tree, '<h2>', '</h2>');}
	function parse_h3($tree)    {return $this->simple_parse($tree, '<h3>', '</h3>');}
	function parse_h4($tree)    {return $this->simple_parse($tree, '<h4>', '</h4>');}
	function parse_center($tree)    {return $this->simple_parse($tree, '<center>', '</center>');}
	
	function ignore_tag($tree) { return $this->simple_parse($tree, '', ''); }
	function remove_tag($tree) { return ''; }
	
	function parse_file($tree, $params = array()) {
  	/* [file]http://website.com/files/some_file.pdf[/file]
		or [file=http://website.com/files/some_file.pdf]Link name[/file] is supported
		*/
		$file = isset($params['file']) ? $params['file'] : $tree->toText();
		$info = pathinfo_utf($file);
		$type = $info['extension'];
		$size = (file_exists($file)) ? filesize($file) : 0;
		$bytes = ($size) ? ' (' . ($size / 1025) . 'KiB' . ')' : '';
		
		$class = 'file ' . $type;
		$additional = $bytes;

		return $this->simple_parse($tree, '<a href="' . $file . '" class="' . $class . '"><span class="icon"><b>['.$type.']</b></span> <span class="label">', '</span></a>' . $additional);
	}
}

function pathinfo_utf($path) 
{ 
	if (strpos($path, '/') !== false) {
    $parts = explode('/', $path);
    $basename = end($parts); 
  }
	elseif (strpos($path, '\\') !== false) {
    $parts = explode('\\', $path);
    $basename = end($parts); 
  }
	else
    return false; 

	if (empty($basename)) return false; 

  $len1 = strlen($path);
  $len2 = strlen($basename);
	$dirname = substr($path, 0, $len1 - $len2 - 1); 

	if (strpos($basename, '.') !== false) 
	{ 
    $parts = explode('.', $path);
		$extension = end($parts); 
		$filename = substr($basename, 0, strlen($basename) - strlen($extension) - 1); 
	} 
	else 
	{ 
		$extension = ''; 
		$filename = $basename; 
	} 

	return array 
	( 
		'dirname' => $dirname, 
		'basename' => $basename, 
		'extension' => $extension, 
		'filename' => $filename 
	); 
} 

