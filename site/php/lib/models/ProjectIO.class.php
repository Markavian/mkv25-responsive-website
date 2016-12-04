<?php

class ProjectIO
{
	static $FILE_INDEX = array();

	public static function readProjectFromFile($urlName)
	{
		$project = false;

		if(ProjectIO::checkIfProjectExists($urlName))
		{
			$filePath = ProjectIO::getFilePathFor($urlName);

			$projectXML = simplexml_load_file($filePath);
			$project = Project::createFromXHTML($projectXML);
		}

		return $project;
	}

	public static function readAllProjects()
	{
		$projects = Array();

		$directoryPath = ProjectIO::getDirectoryPath();

		$fileList = scandir($directoryPath);

		foreach($fileList as $key => $fileName)
		{
			if (endsWith($fileName, 'project.xml'))
			{
				if (isset(ProjectIO::$FILE_INDEX[$fileName]))
				{
					$project = ProjectIO::$FILE_INDEX[$fileName];
				}
				else
				{
					$filePath = $directoryPath . $fileName;
					$projectXML = simplexml_load_file($filePath);
					$project = Project::createFromXHTML($projectXML);

					ProjectIO::$FILE_INDEX[$fileName] = $project;
				}

				if ($project)
				{
					$projects[] = $project;
				}
			}
		}

        header('mkv25-project-count: ' . count($projects));
        header('mkv25-project-filelist: ' . $directoryPath);

		return $projects;
	}

	public static function checkIfProjectExists($urlName)
	{
		$filePath = ProjectIO::getFilePathFor($urlName);

		return file_exists($filePath);
	}

	private static function getFilePathFor($urlName)
	{
		$fileName = ProjectIO::getFileNameFor($urlName);
		$directoryPath = ProjectIO::getDirectoryPath();

		return $directoryPath . $fileName;
	}

	private static function getDirectoryPath()
	{
		$PROJECT_CONTENT_DIRECTORY = Environment::get('PROJECT_CONTENT_DIRECTORY');

		return __DIR__ . '/../../../' . $PROJECT_CONTENT_DIRECTORY . '/';
	}

	public static function getFileNameFor($urlName)
	{
		$sanitizedName = preg_replace("/[^A-Za-z0-9\_\-]/", '', $urlName);
		$sanitizedName = strtolower($sanitizedName);
		$fileName = sprintf("%s.project.xml", $sanitizedName);

		return $fileName;
	}
}
