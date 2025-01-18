<?php

namespace ln\threecx;

class Config
{
	public function __construct()
	{
	}
	public string $fqdn;
	public int $port = 443;
	public string $user;
	public string $password;
	public string $mfa = "";
	public bool $debug = false;
	public Token $token;

}

class Token
{
	public function __construct()
	{
	}
	public string $tokenType = "";
	public int $expires = 0;
	public string $accessToken = "";
	public string $refreshToken = "";
}
