<?php

namespace App\Http\Requests\Api\Aggregator\Doctors;

class BookingAppointment extends \App\Http\Requests\Api\BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'hospital_code' => 'required|exists:hospitals,hospital_code',
            'doctor_code' => 'required|exists:doctors,doctor_code',
            'start_date' => 'date_format:'.config('api.aggregator_api.date_format.input').'|after:today',
            'end_date' => 'date_format:'.config('api.aggregator_api.date_format.input').'|after:today'
        ];
    }

    /**
     * Add parameters to be validated
     * 
     * @return array
     */
    public function all()
    {
        return array_replace_recursive(
            parent::all(),
            $this->route()->parameters()
        );
    }
}
