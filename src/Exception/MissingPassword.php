<?php

namespace ln\threecx;

class MissingPassword extends \Exception
{
	public function __construct()
	{
		parent::__construct("missing password");
	}
}
