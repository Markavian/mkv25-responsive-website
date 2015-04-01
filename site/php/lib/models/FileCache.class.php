<?php

class FileCache 
{
	public static function ageOfCache($cacheId)
	{
		$age = false;

		$path = FileCache::getFilePathFor($cacheId);
		if(file_exists($path))
		{
			$mtime = filemtime($path);
			$age = time() - $mtime;
		}

		return $age;
	}

	public static function readDataFromCache($cacheId)
	{
		$data = false;

		$path = FileCache::getFilePathFor($cacheId);

		if(file_exists($path))
		{
			$cacheContents = file_get_contents($path);
			$data = unserialize($cacheContents);
		}

		return $data;
	}

	public static function storeDataInCache($data, $cacheId)
	{
		$success = false;

		$path = FileCache::getFilePathFor($cacheId);

		if($data)
		{
			$cacheContents = serialize($data);
			$file = @fopen($path, 'w');
			@fwrite($file, $cacheContents);
			@fclose($file);

			$success = true;
		}

		return $success;
	}

	private static function getFilePathFor($cacheId)
	{
		$CACHED_CONTENT_DIRECTORY = Environment::get('CACHED_CONTENT_DIRECTORY');

		return __DIR__ . '/../../' . $CACHED_CONTENT_DIRECTORY . '/' . $cacheId . '.cache';
	}
}