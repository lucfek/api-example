<?php

namespace TgUtils\Auth;

class DefaultCredentialsProvider implements CredentialsProvider {
 
    private $username;
    private $password;
    
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    
	/**
	  * Returns the username.
	  * @return string the username
	  */
    public function getUsername(){
        return $this->username;
    }

	/**
	  * Returns the password.
	  * @return string the password
	  */
    public function getPassword() {
        return $this->password;
    }
    
}