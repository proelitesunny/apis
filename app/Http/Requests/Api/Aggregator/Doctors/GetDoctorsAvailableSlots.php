<?php

namespace App\Http\Requests\Api\Aggregator\Doctors;

use Illuminate\Foundation\Http\FormRequest;
use App\MyHealthcare\Validators\Aggregator\AggregatorValidator;

class GetDoctorsAvailableSlots extends \App\Http\Requests\Api\BaseRequest
{
    public function __construct(array $query = array(),
            array $request = array(),
            array $attributes = array(),
            array $cookies = array(),
            array $files = array(),
            array $server = array(),
            $content = null,
            AggregatorValidator $aggregatorValidator) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        
        $aggregatorValidator->checkDateFormat();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->replace($this->only(['doctor_id','hospital_id','appointment_date','include']));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'doctor_id' => 'required',
            'hospital_id' => 'required',
            'appointment_date' => 'required|check_date_format',
            'include' => 'nullable|in:doctor'
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'include.in' => trans('errors.SLOT_101'),
        ];
    }
}
