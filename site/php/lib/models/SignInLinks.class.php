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
		if($this->user && $this->user->isValidSession())
		{
			$template = new Template('signout-link.template.html');
			$template->set('{LOGOUT_USERNAME}', $this->user->username);
		}
		else
		{
			$template = new Template('signin-link.template.html');
		}
		
		return $template->expand();
	}
}
