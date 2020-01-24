<?php

namespace App\Http\Requests\Api\Aggregator\Doctors;
use App\MyHealthcare\Validators\Aggregator\AggregatorValidator;

class GetAppointments extends \App\Http\Requests\Api\BaseRequest
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
        $this->replace($this->only(['start_date','end_date','patient_id']));
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
            'start_date'=> 'before:'.config('api.aggregator_api.bookings.start_date').'|check_date_format',
            'end_date'=> 'after:start_date|check_date_format',
            'patient_id'=>'integer|min:1'
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
            'start_date.before' => trans('errors.APPOINTMENT_103')
        ];
    }
}
