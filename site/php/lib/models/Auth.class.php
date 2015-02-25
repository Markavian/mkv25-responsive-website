<?php

class Auth
{
	public function __construct()
	{
		$this->sql = Sql::getInstance();
	}

	public function getUserByUsername($username)
	{
		$processedUsername = Auth::sanitizeUsername($username);
		if($username != $processedUsername) {
			throw new Exception("Username contains invalid characters.");
		}

		$query = sprintf("SELECT * FROM `dfma_users` WHERE username='%s'", $processedUsername);
		$result = $this->sql->fetch_query($query, 'user:' . $username);

		$user = new User($username);
		$user->md5password   = $result['md5password'];
		$user->dateSignup    = $result['dateSignup'];
		$user->dateLastLogin = $result['dateLastLogin'];
		$user->email         = $result['email'];
		$user->accessLevel   = $result['accessLevel'];

		return $user;
	}

	public static function sanitizeUsername($username) 
	{
		return preg_replace("/[^a-zA-Z0-9]+/", "", $username);
	}
}
