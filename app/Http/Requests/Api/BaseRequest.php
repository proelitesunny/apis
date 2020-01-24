<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

use DateTime;

class BaseRequest extends FormRequest
{
	protected function setInternalDateFormat($field)
	{
        $this->request->set($field, DateTime::createFromFormat(config('api.aggregator_api.date_format.input'), $this->{$field})->format(config('api.aggregator_api.date_format.internal')));
	}

    protected function setInternalTimeFormat($field)
    {
        $this->request->set($field, DateTime::createFromFormat(config('api.aggregator_api.time_format.input'), $this->{$field})->format(config('api.aggregator_api.time_format.internal')));
    }
        
    public function all()
    {
        /*
         * Fixes an issue with FormRequest-based requests not
         * containing parameters added / modified by middleware
         * due to the FormRequest copying Request parameters
         * before the middleware is run.
         *
         * See:
         * https://github.com/laravel/framework/issues/10791
         */
        $this->merge( $this->request->all() );

        return parent::all();
    }

    /**
     * Check if input is present in ParameterBag Object if it's not present in request
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key)
    {
        if (!parent::has($key)) {

            $keys = is_array($key) ? $key : func_get_args();

            foreach ($keys as $value) {
                if ($this->isEmptyStringInBag($value)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Determine if the given input key is an empty string for "has".
     *
     * @param  string  $key
     * @return bool
     */
    protected function isEmptyStringInBag($key)
    {
        $value = $this->request->get($key);

        $boolOrArray = is_bool($value) || is_array($value);

        return ! $boolOrArray && trim((string) $value) === '';
    }

    protected function getIdTypeValue() {

        if (isset($this->aadhar_number) && !empty($this->aadhar_number)) {
            $this->request->set('id_type', 0);
            $this->request->set('id_number', $this->aadhar_number);
        } else if (isset($this->pan_number) && !empty($this->pan_number)) {
            $this->request->set('id_type', 1);
            $this->request->set('id_number', $this->pan_number);
        } else if (isset($this->passport_number) && !empty($this->passport_number)) {
            $this->request->set('id_type', 3);
            $this->request->set('id_number', $this->passport_number);
        }

        $this->offsetUnset('aadhar_number');
        $this->offsetUnset('pan_number');
        $this->offsetUnset('passport_number');
    }
}
