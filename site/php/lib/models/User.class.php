<?php

class User {
	
	var $username;
	var $md5password;
	var $dateSignup;
	var $dateLastLogin;
	var $email;
	var $accessLevel;

	public function __construct($username='unknown')
	{
		$this->username      = $username;
		$this->md5password   = false;
		$this->dateSignup    = false;
		$this->dateLastLogin = false;
		$this->email         = false;
		$this->accessLevel   = false;
	}
	
}