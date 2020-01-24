<?php

namespace App\Http\Requests\Api\Aggregator\Doctors;

class TimeSlotsRequest extends \App\Http\Requests\Api\BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->replace($this->only(['date']));

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
            'doctorCode' => 'required|exists:doctors,doctor_code',
            'hospital_code' => 'required|exists:hospitals,hospital_code',
            'date' => 'required|date_format:'.config('api.aggregator_api.date_format.input').'|after:yesterday'
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'date.required' => trans('errors.DOCTORS_102'),
            'date.date_format' => trans('errors.DOCTORS_103'),
            'date.after' => trans('errors.DOCTORS_103')
        ];
    }

    public function validate()
    {   
        parent::validate();

        $this->setInternalDateFormat('date');
    }
}
