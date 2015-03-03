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

/* UbbAdminParser class which enabled site admins to input
 * plain html into their messages
 */
class UbbAdminParser extends UbbParser
{
	function UbbAdminParser($tagsToIgnore=array(), $tagsToRemove=array())
	{
		$this->UbbParser($tagsToIgnore, $tagsToRemove);
	}

  function parse_html($tree)
  {
    return $tree->toText();
  }

	/* Additional custom codes for use on mkv25.net with neuro 21/10/2009 */
	/* Deemed a security hazard because it writes out javascript tags */
	function parse_flash($tree, $params = array()) {
  	/* [flash width=100% height=400]http://mkv25.net/showcase/asteroids_v4.swf[/flash] is supported */
		$src = isset($params['src']) ? $params['src'] : $tree->toText();
		$wmode = isset($params['wmode']) ? $params['wmode'] : 'transparent';
		$width = $params['width'];
		$height = $params['height'];
		$base = isset($params['base']) ? $params['base'] : '';
		$id = isset($params['id']) ? $params['id'] : 'flashContainer';
		
		$NL = '';
		$output = '<div id="' . $id . '"></div>' . $NL;
		$output .= '<script type="text/javascript">' . $NL
			//. 'var swfobject;' . $NL
			. 'if(swfobject) { ' . $NL
			. '	flashvars = {};' . $NL
			. '	params = {' . $NL
			.	'		wmode: "' . $wmode . '",' . $NL
			.	'		base: "' . $base . '"' . $NL
			.	'	};' . $NL
			. '	attributes = {};' . $NL
			. '	swfobject.embedSWF("' . $src .'", "' . $id . '", "' . $width . '", "' . $height . '", "9.0.0", false, flashvars, params, attributes);' . $NL
			. '} else { document.write("SWFObject javascript include not found - could not display: ' . $src . '"); }' . $NL
			. '</script>' . $NL;
			
		if($this->valid_url($src))		 
			return $output;
		return $src;
	}
	
	/* General parser to create URL friendly links from unknown bbcode tags */
	function general_url_parse($tree, $params = array())
	{
		$text = isset($params['src']) ? $params['src'] : $tree->toText();
		$class = isset($params['class']) ? $params['class'] : 'qbb';
		$value = isset($params[$class]) ? $params[$class] : $text;
		
		$class = strtolower($class);
		$href = $this->siteroot . htmlspecialchars($class . '-' . $value);
		
		$NL = '';
		$output = '<a class="' . $class . '" href="' . $href . '">' . $text . '</a>';
			
		if($this->valid_url($href))		 
			return $output;
		return $text;
	}
}

/*
 *
 */

/* StackItems is an recursive object used to create a 
 * tree, from which html or plain text can be derived.
 * Although methods are commented, editing is not
 * recommanded
 */
class stackItem
{
    /* $parent maintaince a link to the parent object of
     * element, as where $childs is an mixed array of plain
     * text and other stackItem objects
     */
    var $parent;
    var $childs;
    /* $tag_open : the ubb tag, without parameters
     * $tag_close: the ubb closing tag.
     * $tag_full : full ubb tag as found in the original
     *             unparsed text
     */
    var $tag_open, $tag_close, $tag_full;
    
    var $was_closed = false;
    /* storeage array for parameter information*/
    var $parameters;
    
    /* construtor initializes attributes */
    function stackItem()
    {
      $this->parent = null;
      $this->childs = array();
      $this->parameters = array();
      $this->tag_open = '';
      $this->tag_close = '';
      $this->tag_full = '';
    }
    
    /* set the parent of the object, this method is often
     * calles upon, just after creation of the object */
    function setParent(&$parent)
    {
      if(!is_object($parent)) return false;
      if(get_class($parent) != get_class($this)) return false;
      $this->parent = $parent;
      return true;
    }
    
    /* Alter $this->tag_open and $this->tag_close from an
     * external scope */
    function setTag($open, $close = '')
    {
       $this->tag_open = strtolower($open);
       $this->tag_close = strtolower($close);
    }
    
    /* parse $text until $this->tag_close is encountered.
     * When a other closing tag than expected is found, 
     * handle it appropriate:
     * - Look down the tree, werther there is an element for
     *   which the found closing tag is appropriate. If this
     *   element is less then UBB_LOOKDOWN steps away, close
     *   the current tag and return to calling object. When
     *   out of range, handle the closing tag as ordinary 
     *   text
     */
    function take($text)
    {
      while(($s = strpos($text, '[')) >= 0 && strlen($text) > 0)
      {
        if($s===false)
        {
          $this->append($text);
          $text = '';
        }
        elseif($s == 0)
        {
          $close = strpos($text, ']');
          if($close < 0)
          {
            $this->append($text);
            $text = '';
          }
          elseif(substr($text, 0, 2) == '[/')
          {
            $tag = strtolower(substr($text, 0, $close+1));
            $text = substr($text, $close+1);
            if($tag==$this->tag_close)
            {
              $this->was_closed = true;
              return $text;
            }
            else if($this->parent != null)
            {
              $subelem = $this->parent->isThisYours($tag, UBB_LOOKDOWN);
              if(!$subelem)
              {
                $this->append($tag);
              }
              else
              {
                if($subelem <= UBB_LOOKDOWN)
                {
                  return $tag.$text;
                }
                else
                {
                  $this->append($tag);
                }
              }
            }
            else
            {
              $this->append($tag);
            }
          }
          else
          {
            $child = new stackItem();
            $child->setParent($this);
            $text = $child->build($text);
            $this->append($child);
          }
        }
        else
        {
          $this->append(substr($text, 0, $s));
          $text = substr($text, $s);
        }
        $s = -1;
      } //end while
      
      return $text;
    }
    
    /**
    * parse $tag into $tag_open en $tag_full.
    * extract (parameter,value) pairs and store
    * these in $this->parameters;
    */
    function parseTag($tag)
    {
      $this->tag_full = '['.$tag.']';
      while(strpos($tag, ' =') > 0) $tag = str_replace(' =', '=', $tag);
      while(strpos($tag, '= ') > 0) $tag = str_replace('= ', '=', $tag);
      while(strpos($tag, ', ') > 0) $tag = str_replace(', ', ',', $tag);
      while(strpos($tag, ' ,') > 0) $tag = str_replace(' ,', ',', $tag);
      $exploded = explode(' ', $tag);
      $tag_open = '';
      foreach($exploded as $index => $element)
      {
        $pair = explode('=', $element, 2);

        if(count($pair) == 2)
        {
          $this->parameters[strtolower($pair[0])] = $pair[1];
        }
        if($index == 0) $tag_open = $pair[0];
      }
      $this->tag_open = strtolower($tag_open);
      $this->tag_close = strtolower('[/'.$tag_open.']');
    }
    
    /* build($text) generates a tree from $text from where
     * $this is the current root element.
     */
    
    function build($text)
    {
      if(empty($text)) return '';

      if(substr($text, 0, 1) == '[')
      {
         /* Starts with an tag?
          * parsing should stop when /tag is found
          *
          * therefor $tag_open, $tag_close should be
          * initialized
          */
        $sclose = strpos($text, ']');
        if($sclose<0)
        {
          $this->append($text);
          return '';
        }
        $tag = substr($text, 1, $sclose-1);

        $text = substr($text, $sclose + 1);
        $this->parseTag($tag);
        if(_quickerUBB_isTextTag(strtolower($tag)))
        {
          $s = strpos(strtolower($text),'[/'.strtolower($tag));
          if($s == false)
          {
            $text = $this->take($text);
          }
          else
          {
            $subtext = substr($text, 0, $s);
            $this->childs[] = $subtext;
            $text = substr($text, $s);
            $text = substr($text, strpos($text,']')+1);
          }
        }
        else
        {
          $text = $this->take($text);
        }
        return $text;
      }
      else
      {
        /* Starts with text, therefor containerobject
         */
        $text = $this->take($text);
        $this->append($text);
      }
    }
    
    /* appends $data to the internal leaf structure. 
     * $data can be object or plain text
     */
    function append($data)
    {
      if(empty($data)) return;
      $this->childs[] = $data;
    }
    
    /* This method is called upon from child object, to
     * find a object matching to a found closing tag 
     * in order to maintain a stable structure.
     *
     * returns 'false' or a numeric value, telling the
     * calling child how many levels the corresponding
     * element is down in the tree, from the childs origin
     */
    function isThisYours($closingTag, $was_closed = 0)
    {
      if($closingTag == $this->tag_close)
      {
        if($was_closed >= 0) { $this->was_closed = true;}
        return 1;
      }
      if($this->parent == null)
      {
        return false;
      }
      else
      {
        $s = $this->parent->isThisYours($closingTag, $was_closed - 1);
        if(is_int($s)) return $s + 1;
        return $s;
      }
      
    }
    /* Return the parameters for this object */
    function getParameters()
    {
      return $this->parameters;
    }
    
    /* Return a string representation of this tag in plain
     * ubb */
    function toString()
    {
      return $this->tag_full.$this->toText().($this->was_closed ? $this->tag_close : '');
    }
    
    /* Return a string representation of this tags inner
     * in plain ubb */
    function toText()
    {
      $text = '';
      foreach($this->childs as $c)
      {
        if(is_object($c))
        {
          $text.= $c->toString();
        }
        else
        {
          $text.= $c;
        }
      }
      return $text;
    }
    
    /* convert the contents of this element to html.
     * the $parser object is used to find appropriate
     * parse_tag methods.
     */
    function innerToHtml(&$parser, $methods = array())
    {
      $text = '';
      foreach($this->childs as $c)
      {
        if(is_object($c))
        {
          $text.= $c->parse($parser, $methods);
        }
        else
        {
          $text.= $parser->parse_text($c);
        }
      }
      return $text;

    }
    
    /* Convert the total object to html */
    function toHtml(&$parser, $methods=array(), $inner_only = true)
    {
      $text = '';
      if(strlen($this->tag_full) > 0 && !$inner_only)
      {
        if(isset($methods[$this->tag_open]))
        {
          $method = $methods[$this->tag_open];
          $text = $parser->$method($this);
        }
        else
        {
          return $this->innerToHtml($parser, $methods);
        }
      }
      else
      {
        /* No method found for this tag */
        foreach($this->childs as $c)
        {
          if(is_object($c))
          {
            $text.= $c->parse($parser, $methods);
          }
          else
          {
            $text.= $parser->parse_text($c);
          }
        }
      }
      return $text;
    }
    
    /* Parse this object into html, this method is called
     * from the root element of the constructed tree */
    function parse(&$parser, $methods = array())
    {
      $text = '';
      if(strlen($this->tag_full) > 0)
      {

        if(isset($methods[$this->tag_open]))
        {
          $method = $methods[$this->tag_open];
          $text = $parser->$method($this, $this->parameters);
        }
				else
				if(isset($methods['general_url_parse']))
				{
					$method = $methods['general_url_parse'];
					$this->parameters['class'] = $this->tag_open;
          $text = $parser->$method($this, $this->parameters);
				}
        else
        {
          foreach($this->childs as $c)
          {
            if(is_object($c))
            {
              $text.= $c->parse($parser, $methods);
            }
            else
            {
              $text.= $parser->parse_text($c);
            }
          }
          return $this->tag_full.$text.($this->was_closed ? $this->tag_close : '');
        }
      }
      else
      {
        /* No method found for this tag */
        foreach($this->childs as $c)
        {
          if(is_object($c))
          {
            $text.= $c->parse($parser, $methods);
          }
          else
          {
            $text.= $parser->parse_text($c);
          }
        }
      }
      return $text;
    }
}
