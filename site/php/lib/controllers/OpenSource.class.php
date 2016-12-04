<?php

class OpenSource
{
	public function render($request)
	{
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Open Source');
		$view->eyecatch('Open Source', "A round up of mkv25.net's open source projects across the web.");
		$view->banner('open-source short');

        $content = Content::load('content/open-source.content.md');
        $view->addSingleColumn($content);

        $projects = ProjectIO::readAllProjects();
        $organizations = Project::organizations();

        foreach($organizations as $key => $projects) {

            $content = Content::load('content/organizations/' . $key . '.content.md');
            $view->addSingleColumn($content);

            foreach($projects as $key => $project) {
                $view->addSingleColumn($project->render());
            }
        }

		return $view->render();
	}
}