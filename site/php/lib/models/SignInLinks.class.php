<?php

class SignInLinks
{
	var $user;

	function __construct($user)
	{
		$this->user = $user;
	}
	
	public function render()
	{
		global $CLEF_AUTH;
		global $CLEF_PATHS;

		if($this->user && $this->user->isValidSession())
		{
			$template = new Template('site/templates/logout-link.template.html');
			$template->set('{LOGOUT_USERNAME}', $this->user->username);
		}
		else
		{
			$template = new Template('site/templates/login-link.template.html');

			$template->set('{CLEF_APP_ID}', $CLEF_AUTH['appId']);
			$template->set('{CLEF_REDIRECT_URL}', $CLEF_PATHS['redirectUrl']);
		}
		
		return $template->expand();
	}
}
