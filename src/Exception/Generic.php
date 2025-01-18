<?php

namespace ln\threecx;

class Generic extends \Exception
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}
