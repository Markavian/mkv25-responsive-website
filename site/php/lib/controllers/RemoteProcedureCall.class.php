<?php

class RemoteProcedureCall
{
	public function render($request)
	{
		$remoteProcedureName = $request->page_field;
		$token = $request->page_value;

		if($remoteProcedureName)
		{
			if($token)
			{
				$result = $this->processRemoteProcedureCall($remoteProcedureName, $token);
			}
			else
			{
				$message = "Method '$remoteProcedureName' not allowed, no token supplied";
				$result = $this->render501NotSupportedResponse($message);
			}
		}
		else
		{
			$message = "No method specified";
			$result = $this->render501NotSupportedResponse($message);
		}

		return $result;
	}

	private function processRemoteProcedureCall($remoteProcedureName, $token)
	{
		$argsArray = RemoteProcedureCall::retrieveCallbackArgs($remoteProcedureName, $token);

		if($argsArray)
		{
			$message = "Accepted call: $remoteProcedureName for $token";
			$result = $this->render202AcceptedResponse($message);

			$callable = array($this, $remoteProcedureName);

			call_user_func_array($callable, $argsArray);
		}
		else
		{
			$message = "Method '$remoteProcedureName' not allowed, invalid token.";
			$result = $this->render501NotSupportedResponse($message);
		}

		return $result;
	}

	private function getTwitterUser($userId)
	{
		$connection = TwitterReader::createTwitterOAuthConnection();
		if (!$connection) return false;
		
		$userInfo = $connection->get("users/show", array("user_id" => $userId, "trim_user" => 1, "count" => 20));
		FileCache::storeDataInCache($userInfo, "user.$userId");
	}

	private function getTweetsForUser($userId)
	{
		$connection = TwitterReader::createTwitterOAuthConnection();
		if (!$connection) return false;

		$tweets = $connection->get("statuses/user_timeline", array("user_id" => $userId, "trim_user" => 1, "count" => 20));
		FileCache::storeDataInCache($tweets, 'tweets');
	}

	private function render202AcceptedResponse($message)
	{
		$view = new DefaultView();
		$view->responseCode(202, $message);
		
		return $view->render();
	}

	private function render501NotSupportedResponse($message)
	{
		$view = new DefaultView();
		$view->responseCode(501, $message);
		
		return $view->render();
	}

	public static function makeRemoteCall($methodName, $argsArray)
	{
		$request = Environment::get('REQUEST');
		$baseUrl = $request->base;

		$token = RemoteProcedureCall::registerCallback($methodName, $argsArray);
		if($token)
		{
			$urlParts = array('http://', $_SERVER['SERVER_NAME'], $baseUrl, 'remote/procedure/', $methodName, '-', $token);
			$rpcUrl = implode("", $urlParts);

			RemoteProcedureCall::curlPostAsync($rpcUrl);
		}
	}

	private static function curlPostAsync($url, $params=false)
	{
		$post_params = array();
		if(is_array($params))
		{
		    foreach ($params as $key => &$val)
		    {
				if (is_array($val)) $val = implode(',', $val);
				$post_params[] = $key.'='.urlencode($val);
		    }
		}
	    $post_string = implode('&', $post_params);

	    $parts=parse_url($url);

	    $fp = fsockopen($parts['host'],
	        isset($parts['port'])?$parts['port']:80,
	        $errno, $errstr, 30);

	    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
	    $out.= "Host: ".$parts['host']."\r\n";
	    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
	    $out.= "Content-Length: ".strlen($post_string)."\r\n";
	    $out.= "Connection: Close\r\n\r\n";
	    if (isset($post_string)) $out.= $post_string;

	    fwrite($fp, $out);
	    fclose($fp);
	}

	private static function registerCallback($methodName, $argsArray)
	{
		$token = md5($methodName . print_r($argsArray, true));

		$cacheName = sprintf("%s-%s", $methodName, $token);

		// Check for evidence of existing RPC token for this specific method name and arguments
		if(FileCache::ageOfCache($cacheName))
		{
			$token = false;
		}
		else
		{
			FileCache::storeDataInCache($argsArray, $cacheName);
		}

		return $token;
	}

	private static function retrieveCallbackArgs($methodName, $token)
	{
		$cacheName = sprintf("%s-%s", $methodName, $token);

		$argsArray = FileCache::readDataFromCache($cacheName);

		FileCache::removeCache($cacheName);

		return $argsArray;
	}
}