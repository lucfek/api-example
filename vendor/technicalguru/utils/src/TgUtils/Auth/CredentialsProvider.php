<?php

namespace TgUtils\Auth;

/**
  * A helper interface when user credentials are required.
  */
interface CredentialsProvider {

	/**
	  * Returns the username.
	  * @return string the username
	  */
	public function getUsername();

	/**
	  * Returns the password.
	  * @return string the password
	  */
	public function getPassword();

}

