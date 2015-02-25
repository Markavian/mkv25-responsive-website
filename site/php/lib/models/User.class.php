<?php

class User {
	
	var $username;
	var $nickname;
	var $md5password;
	var $dateSignup;
	var $dateLastLogin;
	var $dateLoggedOut;
	var $email;
	var $clefId;
	var $accessLevel;

	public function __construct($username='unknown')
	{
		$this->username      = $username;
		$this->nickname      = false;
		$this->md5password   = false;
		$this->dateSignup    = false;
		$this->dateLastLogin = false;
		$this->dateLoggedOut = false;
		$this->email         = false;
		$this->clefId        = false;
		$this->accessLevel   = false;
	}

	public function isValidSession()
	{
		if($this->dateLastLogin)
		{
			if($this->dateLoggedOut)
			{
				// Convert dates to unix timestamps
				$lastLogin = new DateTime($this->dateLastLogin);
				$loggedOut = new DateTime($this->dateLoggedOut);

				// User may have been logged out
				return ($lastLogin >= $loggedOut);
			}

			// Last login within sensible range
			return true;
		}

		// Date last logged in invalid
		return false;
	}

	public function displayName()
	{
		return ($this->nickname) ? $this->nickname : $this->username;
	}

}