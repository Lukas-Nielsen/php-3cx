<?php

namespace ln\threecx;

class MissingUser extends \Exception
{
	public function __construct()
	{
		parent::__construct("missing user");
	}
}
