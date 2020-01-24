<?php

namespace App\MyHealthcare\Validators\Aggregator;

use Illuminate\Contracts\Validation\Factory;

class AggregatorValidator {

    private $validationFactory;
    public $errorMessage;

    public function __construct(Factory $factory) {
        $this->validationFactory = $factory;
        $this->errorMessage = "Something went wrong, Please try again";
    }

    protected function checkKeyValue($array, $key, $val) {
        foreach ($array as $item) {
            if ((isset($item[$key])) && ($item[$key] === $val))
                return true;
        }
        return false;
    }

    public function checkDateFormat() {
        $this->validationFactory->extend(
                'check_date_format', function ($attribute, $value, $parameters) {
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $value)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }, trans('errors.PATIENT_127')
        );
        return $this;
    }
    
}
