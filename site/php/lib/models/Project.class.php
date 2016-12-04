<?php

class Project
{
	static $NAME_INDEX = array();
    static $ORGANIZATION_INDEX = array();

	var $name;
	var $organization;
	var $description;
	var $languages;
	var $link;

	public function __construct()
	{
		$this->name           = false;
    	$this->organization   = false;
		$this->description    = false;
		$this->languages      = false;
		$this->link           = false;
	}

	public function readFromSimpleXmlDocument($simpleXmlDocument)
	{
		$doc = $simpleXmlDocument;

        $this->name           = (string)$doc->name;
        $this->organization   = (string)$doc->organization;
        $this->description    = (string)$doc->description;
        $this->languages      = (string)$doc->languages;
        $this->link           = (string)$doc->link;

		$this->languages = explode(",", (string)$doc->languages);

		Project::$NAME_INDEX[$this->name] = $this;
        Project::indexByOrganization($this);
	}

    static public function organizations() {
        return Project::$ORGANIZATION_INDEX;
    }

    static public function indexByOrganization($project) {
        if(empty(Project::$ORGANIZATION_INDEX[$project->organization])) {
            Project::$ORGANIZATION_INDEX[$project->organization] = array();
        }
        Project::$ORGANIZATION_INDEX[$project->organization][] = $project;
    }

	static public function createFromXHTML($simpleXmlDocument)
	{
		$project = new Project();

		$project->readFromSimpleXmlDocument($simpleXmlDocument);

		return $project;
	}

	public function toXHTML()
	{
		$project = $this;

		$languages = implode(",", $project->languages);

		ob_start();

		echo <<<END
<project>
	<name>$project->name</name>
	<organization>$project->organization</organization>
	<description>$project->description</description>
    <link>$project->link</link>
    <languages>$languages</languages>
</project>
END;

		$xhtml = ob_get_clean();

		$xhtml = str_replace("&", "&amp;", $xhtml);

		return $xhtml;
	}

    public function render() {

		$project = $this;

		$languages = '';
        foreach($project->languages as $key => $language) {
            $languages .= '<li class="' . $language . '">' . $language . '</li>';
        }

		ob_start();

		echo <<<END
<h3>$project->name</h3>
<p>$project->description</p>
<ul><li><a href="$project->link">$project->link</a></li></ul>
<p>Languages:</p>
<ul>$languages</ul>
END;

        return ob_get_clean();
    }
}
