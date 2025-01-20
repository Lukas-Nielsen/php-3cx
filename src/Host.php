<?php

namespace ln\threecx;

class Host
{
	public string $fqdn;
	public int $port = 443;
	public bool $debug = false;
	public function __construct(string $fqdn, int $port = 443, bool $debug = false)
	{
		$this->fqdn = $fqdn;
		$this->port = $port;
		$this->debug = $debug;
	}
}
