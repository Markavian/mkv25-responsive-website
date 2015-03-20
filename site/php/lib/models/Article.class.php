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
		$localUrl = $this->buildUrl($this->contentUrl);
		return Article::renderArticle($this->name, $this->type, $this->contentWidth, $this->contentHeight, $localUrl, $this->category, $this->description, $this->postdate, $simple, $this->displayIcon);
	}

	function buildUrl($contentUrl)
	{
		global $SHOWCASE;

		if($contentUrl && !$this->startsWith($contentUrl, 'http'))
		{
			$contentUrl = $SHOWCASE['baseUrl'] . $this->contentUrl;
		}

		return $contentUrl;
	}

	function startsWith($haystack, $needle) {
	    // search backwards starting from haystack length characters from the end
	    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}

	public static function renderArticle($name, $type, $width, $height, $url, $category, $description, $postdate=false, $simpleMode=false, $icon_url=false)
	{
		ob_start();
		$description = stripslashes($description);
		$posttime = strtotime($postdate);
		
		echo "<heading>$name</heading>";

		if($icon_url)
		{
			?>
			<div class="article icon">
				<div class="icon holder" style="background: url('<?php echo $icon_url; ?>') no-repeat center center;"></div>
			</div>
			<?php
		}
		
		if($posttime)
		{
			?>
		<p class="date">Posted: <?php echo date('Y/m/d H:m:s', $posttime); ?></p>
			<?php
		}

		if($type == 'iframe')
		{
			?> 
			<div class="media">
			<?php
			if($width > 0 && $height > 0)
			{
				$width = '100%';
				?>
				<iframe src="<?php echo $url?>" frameborder="0" scrolling="no" style="width: <?php echo $width?>px; height: <?php echo $height?>px;"> 
		<?php } else { ?>
				<iframe src="<?php echo $url?>" frameborder="0" scrolling="no" style="width: 100%; height: 340px;"> 
		<?php } ?>
				<p>You need to activate IFRAMEs to view this content.</p> 
				</iframe>
			</div>
			<?php echo "\n"?> 
			<?php
		}
		else if($type == 'applet') {
			?> 
			<div class="media">
				<applet code="<?php echo $url?>" archive="http://mkv25.net/applets/<?php echo $url?>/<?php echo $url?>.jar" width=<?php echo $width?> height=<?php echo $height?>>
				<p>You need to have Java 1.5 installed for this applet to work correctly.</p>
				</applet>
			</div>
			<?php echo "\n"?> 
			<?php	
		}
		else if($type == 'image') {
			if($width > 0 && $height > 0) {
			?> 
				<div class="media"><img src="<?php echo $url?>" width="<?php echo $width?>" height="<?php echo $height?>" /></div> 
			<?php
			} else {
			?>
				<div class="media"><img src="<?php echo $url?>" /></div> 
			<?php
			}
			?>
			<?php echo "\n"?> 
			<?php
		}
		
		if($url)
		{
		?> 
		<p>
		<?php if($type == 'applet') { ?>
			<a class="tool sourcecode" href="http://mkv25.net/applets/<?php echo $url?>/<?php echo $url?>.pde" target="_blank"><b>Source code</b></a>
			<a class="tool newwindow" href="http://mkv25.net/applets/<?php echo $url?>" target="_blank"><b>Open content in new tab</b></a>
		<?php } else { ?>
			<a class="tool newwindow" href="<?php echo $url?>" target="_blank"><b>Open content in new tab</b></a>
		<?php } ?>
		</p> 
		<?php
		}
		?>
		<div class="article content">
		<?php echo BBCodeOutput::process($description, $simpleMode); ?>
		</div>
		<?php

		return ob_get_clean();
	}
}
