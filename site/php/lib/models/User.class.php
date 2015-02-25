<?php

class User {
	
	var $username;
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
				// User may have been logged out
				return ($this->dateLastLogin >= $this->dateLoggedOut);
			}

			// Last login within sensible range
			return true;
		}

		// Date last logged in invalid
		return false;
	}

}