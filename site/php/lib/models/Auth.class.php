<?php

class Auth
{
	static $currentUser;

	public function __construct()
	{
		$this->sql = Sql::getInstance();

		if(!Auth::$currentUser) {
			Auth::$currentUser = new User();
			$this->checkSession();
		}
	}

	// Requires: ALTER TABLE  `dfma_users` ADD  `dateLoggedOut` DATETIME NOT NULL AFTER  `dateLastLogin` ;
	// Requires: ALTER TABLE  `dfma_users` ADD  `clefId` BIGINT( 20 ) NOT NULL AFTER  `email` ;
	public function clefLogin($clefId, $firstName=false, $email=false)
	{
		$user = $this->getUserByClefId($clefId);
		$this->logIn($user);
	}

	function getUserByClefId($clefId)
	{
		$user = false;

		$query = sprintf("SELECT * FROM `dfma_users` WHERE clefId='%s' LIMIT 1", $clefId);
		$result = $this->sql->fetch_query($query, 'clefLogin:' . $clefId);

		if($this->sql->num_rows('clefLogin:' . $clefId) == 1)
		{
			$user = $this->createUserFromDatabaseResult($result);
		}

		return $user;
	}

	public function passwordLogin($id, $md5password)
	{
		// TODO: Login a user by an ID
		// Valid ID:
		// - username
		// - email address

		if($user->clefId) {
		    $_SESSION['clef_id'] = $result['clefId'];
		    $_SESSION['logged_in_at'] = time();
		}

		$this->currentUser = $user;
	}

	function getUserByUserId($userId)
	{
		$user = false;

		$processedId = Auth::sanitizeId($userId);
		if($userId != $processedId) {
			throw new Exception("Supplied login details contained invalid characters.");
		}

		$query = sprintf("SELECT * FROM `dfma_users` WHERE (username='%s' || email='%s') && md5password='%s' LIMIT 1", $processedId, $processedId, $md5password);
		$result = $this->sql->fetch_query($query, 'passwordLogin:' . $id);

		if($this->sql->num_rows('passwordLogin:' . $id) == 1)
		{
			$user = $this->createUserFromDatabaseResult($result);
		}

		return $user;
	}

	function createUserFromDatabaseResult($result)
	{
		$username = $result['username'];

		$user = new User($username);
		$user->md5password   = $result['md5password'];
		$user->dateSignup    = $result['dateSignup'];
		$user->dateLastLogin = $result['dateLastLogin'];
		$user->dateLoggedOut = $result['dateLoggedOut'];
		$user->email         = $result['email'];
		$user->clefId        = $result['clefId'];
		$user->accessLevel   = $result['accessLevel'];
	}

	function checkSession()
	{
		$user = false;

		// Check for clef ID first
		if(isset($_SESSION['clef_id'])) {
			$clefId = $_SESSION['clef_id'];
			$user = $this->getUserByClefId($clefId);
		}
		else if(isset($_SESSION['mkv_user_id'])) {
			$userId = $_SESSION['mkv_user_id'];
			$user = $this->getUserByUserId($userId);
		}

		if($user) {
			$this->currentUser = $user;
		}
	}

	public function getCurrentUser()
	{
		return Auth::$currentUser;
	}

	public function logIn($user) 
	{
		if(!$user) {
			return;
		}

		if($user->clefId) {
		    $_SESSION['clef_id'] = $user->clefId;
		    $_SESSION['logged_in_at'] = time();
		}

		if($user->username) {
			$_SESSION['mkv_user_id'] = $user->username;
		}

		Auth::$currentUser = $user;

		// TODO: Update last login date
	}

	public function logOut($user)
	{
		if(!$user) {
			return;
		}

		// TODO: Update the logged out date
	}

	public static function sanitizeId($id) 
	{
		return preg_replace("/[^a-zA-Z0-9@\._-]+/", "", $id);
	}
}
