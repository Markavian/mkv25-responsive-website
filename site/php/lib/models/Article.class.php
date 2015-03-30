<?php

class Article
{	
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

	public function readFrom($sqlResultArray)
	{
		$row = $sqlResultArray;

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
	}

	public function addLinkedArticle($articleId)
	{
		if($articleId && is_array($this->linkedArticles))
		{
			$this->linkedArticles[] = $articleId;
		}
	}

	static public function createFrom($sqlResultArray)
	{
		$article = new Article();

		$article->readFrom($sqlResultArray);

		return $article;
	}

	public function renderFullArticle()
	{
		$simple = false;
		return ArticleFormatter::renderArticle($this->name, $this->type, $this->contentWidth, $this->contentHeight, $this->contentUrl, $this->urlname, $this->category, $this->description, $this->postdate, $simple, $this->displayIcon);
	}
}
