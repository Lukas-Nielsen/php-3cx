<?php

namespace ln\threecx;

class Config
{
	public function __construct(string $fqdn, string $user, string $password, int $port = 443, string $mfa = "", bool $debug = false)
	{
		$this->fqdn = $fqdn;
		$this->user = $user;
		$this->password = $password;
		$this->port = $port;
		$this->mfa = $mfa;
		$this->debug = $debug;
	}
	public string $fqdn;
	public int $port = 443;
	public string $user;
	public string $password;
	public string $mfa = "";
	public bool $debug = false;

}

class Token
{
	public function __construct(string $tokenType, int $expires, string $accessToken, string $refreshToken)
	{
		$this->tokenType = $tokenType;
		$this->expires = $expires;
		$this->accessToken = $accessToken;
		$this->refreshToken = $refreshToken;
	}
	public string $tokenType;
	public int $expires;
	public string $accessToken;
	public string $refreshToken;
}
