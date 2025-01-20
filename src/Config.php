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

class Host
{
	public function __construct(string $fqdn, int $port = 443, bool $debug = false)
	{
		$this->fqdn = $fqdn;
		$this->port = $port;
		$this->debug = $debug;
	}
	public string $fqdn;
	public int $port = 443;
	public bool $debug = false;
}

class Rest
{
	public function __construct(string $clientId, string $clientSecret)
	{
		$this->fqdn = $clientId;
		$this->clientSecret = $clientSecret;
	}
	public string $clientId;
	public string $clientSecret;
}
