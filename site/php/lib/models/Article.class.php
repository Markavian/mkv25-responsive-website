<?php

class Article
{
	static $ID_INDEX = array();
	static $URLNAME_INDEX = array();

	var $id;
	var $urlname;
	var $name;
	var $hits;
	var $description;
	var $keywords;
	var $type;

	var $contentUrl;
	var $width;
	var $height;
	var $category;
	var $displayIcon;

	var $linkedArticles;
	var $postdate;

	public function __construct()
	{
		$this->id             = false;
		$this->urlname        = false;
		$this->name           = false;
		$this->hits           = false;
		$this->description    = false;
		$this->type           = false;

		$this->contentUrl     = false;
		$this->contentWidth   = false;
		$this->contentHeight  = false;
		$this->category       = false;
		$this->displayIcon    = false;

		$this->linkedArticles = array();
		$this->postdate       = false;
	}

	public function readFromIndexedArray($indexedArray)
	{
		$row = $indexedArray;

		$this->id             = $row['id'];
		$this->urlname        = $row['urlname'];
		$this->name           = $row['name'];
		$this->hits           = $row['hits'];
		$this->description    = $row['description'];
		$this->keywords       = $row['keywords'];
		$this->type           = $row['type'];

		$this->contentUrl     = $row['url'];
		$this->contentWidth   = $row['width'];
		$this->contentHeight  = $row['height'];
		$this->category       = $row['category'];
		$this->displayIcon    = $row['icon1'];

		$this->linkedArticles = array();
		$this->addLinkedArticle($row['icon2']);
		$this->addLinkedArticle($row['icon3']);
		$this->addLinkedArticle($row['icon4']);
		$this->addLinkedArticle($row['icon5']);

		$this->postdate       = $row['postdate'];

		Article::$ID_INDEX[$this->id] = $this;
		Article::$URLNAME_INDEX[$this->urlname] = $this;
	}

	public function readFromSimpleXmlDocument($simpleXmlDocument)
	{
		$doc = $simpleXmlDocument;

		$this->id             = (string)$doc->id;
		$this->urlname        = (string)$doc->urlname;
		$this->name           = stripslashes((string)$doc->name);
		$this->hits           = (string)$doc->hits;
		$this->description    = (string)$doc->description;
		$this->keywords       = (string)$doc->keywords;
		$this->type           = (string)$doc->type;

		$this->contentUrl     = (string)$doc->contentUrl;
		$this->contentWidth   = (string)$doc->contentWidth;
		$this->contentHeight  = (string)$doc->contentHeight;
		$this->category       = (string)$doc->category;
		$this->displayIcon    = (string)$doc->displayIcon;

		$this->linkedArticles = explode(",", (string)$doc->linkedArticles);

		$this->postdate       = (string)$doc->postdate;

		Article::$ID_INDEX[$this->id] = $this;
		Article::$URLNAME_INDEX[$this->urlname] = $this;
	}

	public function addLinkedArticle($articleLink)
	{
		if($articleLink && is_array($this->linkedArticles))
		{
			$this->linkedArticles[] = $articleLink;
		}
	}

	static public function createFrom($sqlResultArray)
	{
		$article = new Article();

		$article->readFromIndexedArray($sqlResultArray);

		return $article;
	}

	static public function createFromXHTML($simpleXmlDocument)
	{
		$article = new Article();

		$article->readFromSimpleXmlDocument($simpleXmlDocument);

		return $article;
	}

	public function renderFullArticle()
	{
		$simple = false;
		return ArticleFormatter::renderArticle($this->name, $this->type, $this->contentWidth, $this->contentHeight, $this->contentUrl, $this->urlname, $this->category, $this->description, $this->postdate, $simple, $this->displayIcon);
	}

	public function toXHTML()
	{
		$article = $this;

		$linkedArticles = Article::convertIDArrayToRefArray($article->linkedArticles);
		$linkedArticles = implode(",", $linkedArticles);

		ob_start();

		echo <<<END
<article>
	<id>$article->id</id>
	<urlname>$article->urlname</urlname>
	<name>$article->name</name>
	<hits>$article->hits</hits>
	<description><![CDATA[$article->description]]></description>
	<keywords>$article->keywords</keywords>
	<type>$article->type</type>

	<contentUrl>$article->contentUrl</contentUrl>
	<contentWidth>$article->contentWidth</contentWidth>
	<contentHeight>$article->contentHeight</contentHeight>
	<category>$article->category</category>
	<displayIcon>$article->displayIcon</displayIcon>

	<linkedArticles>$linkedArticles</linkedArticles>
	<postdate>$article->postdate</postdate>
</article>
END;

		$xhtml = ob_get_clean();

		$xhtml = str_replace("&", "&amp;", $xhtml);

		return $xhtml;
	}

	public static function isValidArticle($article)
	{
		return (is_numeric($article->id) && $article->urlname);
	}

	public static function convertIDArrayToRefArray($idArray)
	{
		$refArray = array();

		foreach($idArray as $key=>$value)
		{
			if(isset(Article::$ID_INDEX[$value]))
			{
				$refArticle = Article::$ID_INDEX[$value];
				$refArray[] = $refArticle->urlname;
			}
		}

		return $refArray;
	}
}
