<?php

namespace ln\threecx;

class User
{
	public function __construct(string $username, string $password, string $mfa = "")
	{
		$this->password = $password;
		$this->username = $username;
		$this->mfa = $mfa;
	}
	public string $username;
	public string $password;
	public string $mfa = "";
}
