<?php

namespace App\MyHealthcare\Helpers\MyHealthLogger;

class MyHealthErrorLogger extends \Illuminate\Log\Writer
{

    public function error($message, array $context = [])
    {
        \App\MyHealthcare\Helpers\FlockLogs::logError($message);
        return parent::error($message, $context);
    }

}