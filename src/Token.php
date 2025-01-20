<?php

namespace ln\threecx;

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
