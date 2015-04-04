<?php

class ProxyFallback
{
	var $fallbacks;
	
	public function __construct($fallbackArray)
	{
		$this->fallbacks = Array();
	
		if (is_array($fallbackArray))
		{
			$this->fallbacks = $fallbackArray;
		}
	}
	
	public function __call($name, $arguments)
    {
		$result = false;
		
		foreach($this->fallbacks as $key => $proxy)
		{
			if (method_exists($proxy, $name))
			{
				/* echo sprintf("<pre>Proxy %s : calling %s(%s)</pre>", get_class($proxy), $name, implode(", ", $arguments)); */
				$result = call_user_func_array(array($proxy, $name), $arguments);
			}
			
			if ($result)
			{
				return $result;
			}
		}
		
		return false;
    }
}