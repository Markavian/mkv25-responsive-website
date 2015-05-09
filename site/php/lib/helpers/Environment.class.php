<?php

class Environment
{
	private static $variables;

	public static function initialise()
	{
		Environment::$variables = array();

		Environment::register('REQUEST_TIME_FLOAT', microtime(true));

		$environment = Environment::getEnvironmentVariable('SERVER_ENV', 'stage');

		require('./environment/environment.' . $environment . '.config.php');
	}

	public static function getEnvironmentVariable($key, $default=false)
	{
		$value = getenv($key);
		$value = trim($value);

		return ($value) ? $value : $default;
	}

	public static function register($key, $value)
	{
		Environment::$variables[$key] = $value;
	}

	public static function get($key)
	{
		if(empty(Environment::$variables[$key]))
		{
			throw new Exception('Environment variable ' . $key . ' has not been registered. Use Environment::register($key, $value) before this point.');
		}

		return Environment::$variables[$key];
	}
}
