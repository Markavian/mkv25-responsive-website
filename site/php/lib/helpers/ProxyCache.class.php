<?php

/**
 * Acts a proxy for any object, checking a FileCache based on a unique ID
 * generated from the name of the method, and the arguments supplied.
 * 
 * e.g. usage:
  class MyClass {
    var $counter = 0;
    function count() {
      $this->counter++;
      return $this->counter;
    }
  }
  $myClass = new MyClass();
  echo $myClass->count(); // returns 1
  echo $myClass->count(); // returns 2
 
  $proxyCache = ProxyCache::create($myClass);
  echo $proxyCache->count(); // returns 3
  echo $proxyCache->count(); // returns 3

  
 */
class ProxyCache
{
	var $proxiedObject;
	var $cacheTimeInSeconds;

	public function __construct($reference, $cacheTimeInSeconds=60)
	{
		$this->proxiedObject = $reference;
		$this->cacheTimeInSeconds = $cacheTimeInSeconds;
	}
	
	public function __call($name, $arguments)
    {
		$result = false;
		
		$cacheId = ProxyCache::generateCacheIdFor($name, $arguments);
		$cacheResult = $this->checkCacheForResult($cacheId);
		if ($cacheResult)
		{
			$result = $cacheResult;
		}
		else
		{
			$result = $this->callRealObject($name, $arguments);
			FileCache::storeDataInCache($result, $cacheId);
		}
		
		return $result;
    }
	
	private function checkCacheForResult($cacheId)
	{		
		$result = false;
		
		if(FileCache::ageOfCache($cacheId) < $this->cacheTimeInSeconds)
		{
			$cachedResult = FileCache::readDataFromCache($cacheId);
			
			if ($cachedResult)
			{
				$result = $cachedResult;
			}
		}
		
		return $result;
	}
	
	private function callRealObject($name, $arguments)
	{
		$result = false;
		
		if (method_exists($this->proxiedObject, $name))
		{
			$result = call_user_func_array(array($this->proxiedObject, $name), $arguments);
		}
		
		return $result;
	}
	
	private static function generateCacheIdFor($name, $arguments)
	{
		$flatArguments = print_r($arguments, true);
		$argumentsMd5 = md5($flatArguments);
		
		return sprintf("%s_%s", $name, $argumentsMd5);
	}
	
	public static function create($object, $cacheTimeInSeconds=60)
	{
		$proxyCache = new ProxyCache($object, $cacheTimeInSeconds);
		
		return $proxyCache;
	}
}