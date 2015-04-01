<?php

class AuthLogin
{
	var $clefResult;
	var $authResult;

	public function __construct($request)
	{
		$auth = new Auth();

		// Do a special Clef login if a code is set:
		if(isset($_GET['code']))
		{
			$this->handleClefLogin($auth);
		}
		else {
			$this->clefResult = false;
		}

		// Do a normal login check:
		$this->handleNormalAuth($auth);

		// Start building page response
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Login');
		$view->eyecatch('Login', "Right, lets get you logged in...");
		$view->banner('login');

		// Build up page body
		if($this->clefResult)
		{
			$view->addSingleColumn('Clef Result: ' . $this->clefResult);
		}

		if($this->authResult)
		{
			$view->addSingleColumn('Auth Result: ' . $this->authResult);
		}
		else
		{
			$view->addSingleColumn('TODO: Provide login form.');
		}

		$view->render();
	}

	function handleNormalAuth($siteAuth)
	{
		$user = $siteAuth->getCurrentUser();
		if($user->isValidSession())
		{
			$this->authResult = $user->username . ', Email: ' . $user->email . ', Last login: ' . $user->dateLastLogin . ', Access level: ' . $user->accessLevel;
		}
		else
		{
			$this->authResult = false;
		}
	}

	function handleClefLogin($siteAuth)
	{
		global $CLEF_AUTH;

		// Clef OAuth authorisation
		// Taken from: http://docs.getclef.com/v1.0/docs/quick-setup
		
		$app_id = $CLEF_AUTH['appId'];
		$app_secret = $CLEF_AUTH['appSecret'];

		$code = $_GET["code"];

		$postdata = http_build_query(
			array(
				'code' => $code,
				'app_id' => $app_id,
				'app_secret' => $app_secret
			)
		);

		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
			)
		);

		$url = 'https://clef.io/api/v1/authorize';

		$context  = stream_context_create($opts);
		$response = false;

		set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) {});

		$response = file_get_contents($url, false, $context);
		$response = json_decode($response, true);

		restore_error_handler();

		if ($response) {
			if($response['success']) {
				$access_token = $response['access_token'];

				$opts = array('http' =>
							array(
								'method'  => 'GET'
							)
						);

				$url = 'https://clef.io/api/v1/info?access_token='.$access_token;

				$context  = stream_context_create($opts);
				$response = file_get_contents($url, false, $context);
				$response = json_decode($response, true);

				if ($response && $response['success']) {
					$user_info = $response['info'];
					$this->clefResult = implode($user_info, ', ');
					// {
					//   id: '12345',
					//   first_name: 'Jesse',
					//   last_name: 'Pollak',
					//   phone_number: '1234567890',
					//   email: 'jesse@getclef.com'
					// }

					$siteAuth->clefLogin($user_info['id'], $user_info['first_name'], $user_info['email']);

				} else {
					$this->clefResult = $response['error'];
				}
			} else {
				$this->clefResult = $response['error'];
			}
		}
		else {
			$this->clefResult = 'Unable to retrieve login details.';
		}
	}

	
}
