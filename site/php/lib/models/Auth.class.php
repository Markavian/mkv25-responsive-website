<?php

class Auth
{
	var $userId;
	var $clefId;
	var $emailAddress;
	var $lastLogin;

	public function __construct()
	{
		$this->userId = false;
		$this->clefId = false;
		$this->emailAddress = false;
		$this->lastLogin = false;
	}
}
