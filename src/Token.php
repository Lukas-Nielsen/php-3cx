<?php

namespace ln\threecx;

class Token
{
	public string $tokenType;
	public int $expires;
	public string $accessToken;
	public $refreshToken;
	public function __construct(string $tokenType, int $expires, string $accessToken, $refreshToken)
	{
		$this->tokenType = $tokenType;
		$this->expires = $expires;
		$this->accessToken = $accessToken;
		$this->refreshToken = $refreshToken;
	}
}
