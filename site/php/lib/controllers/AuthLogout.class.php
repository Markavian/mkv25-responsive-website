<?php

class AuthLogout
{
	public function __construct($request)
	{
		global $CLEF_AUTH;

		// Clef OAuth webhook for logout
		// Taken from: http://docs.getclef.com/v1.0/docs/handling-the-logout-webhook

		$app_id = $CLEF_AUTH['appId'];
		$app_secret = $CLEF_AUTH['appSecret'];
		
		if (isset($_POST['logout_token'])) {

			$url = "https://clef.io/api/v1/logout";

			$postdata = http_build_query(
				array(
					'logout_token' => $_POST['logout_token'],
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

			$context  = stream_context_create($opts);
			$response = file_get_contents($url, false, $context);
			$response = json_decode($response, true);

			if($response && $response['success']) {
				// {
				//   clef_id: 172632085,
				//   success: true
				// }

				$clef_id = $response['clef_id'];

				$siteAuth = new Auth();
				$siteAuth->logOut($clef_id);
				$logoutResult = 'Logged out Clef user: ' . $clef_id;
			}
			else {
				$logoutResult = $response['error'];
			}
		}
		else {
			$siteAuth = new Auth();
			$user = $siteAuth->getCurrentUser();
			if($user) {
				$siteAuth->logOut($user);
				$logoutResult = 'All done.';
			}
			else {
				$logoutResult = 'Nothing to do';
			}
		}
		
		$view = new TemplateView();
		$view->baseUrl($request->base);
		$view->title('Logout');
		$view->eyecatch('Logout', "Thanks for visiting...");
		$view->banner('logout');
		
		$view->addSingleColumn('Right, lets get you logged out...');
		$view->addSingleColumn($logoutResult);
		
		$view->render();
	}
}
