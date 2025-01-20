<?php

namespace ln\threecx;

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
