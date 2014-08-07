<?php

namespace Server;

use Exception;

class Error extends Exception
{
	public function __construct($message, $code = 500, $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
