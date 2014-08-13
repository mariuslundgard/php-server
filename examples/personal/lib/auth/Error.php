<?php

namespace Auth;

use Server\Error as Base;

class Error extends Base
{
    protected $fieldErrors;

    public function __construct($message, $fieldErrors = array())
    {
        parent::__construct($message, 401);

        $this->fieldErrors = $fieldErrors;
    }

    public function getFieldErrors()
    {
        return $this->fieldErrors;
    }

    public function dump()
    {
        return array(
            'message' => $this->message,
            'fieldErrors' => $this->fieldErrors
        );
    }
}
