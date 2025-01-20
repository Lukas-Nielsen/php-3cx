<?php

namespace ln\threecx;

class Rest
{
	public string $clientId;
	public string $clientSecret;
	public function __construct(string $clientId, string $clientSecret)
	{
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
	}
}
