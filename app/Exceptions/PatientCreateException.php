<?php

namespace App\Exceptions;

class PatientCreateException extends \Exception
{
    private $errorSource;
    
    public function __construct($message = "", $code = 0, \Exception $previous = null, $errorSource = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorSource = $errorSource;
    }
    
    public function getErrorSource()
    {
        return $this->errorSource;
    }
}