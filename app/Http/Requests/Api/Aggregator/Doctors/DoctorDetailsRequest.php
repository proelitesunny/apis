<?php

namespace App\Http\Requests\Api\Aggregator\Doctors;

class DoctorDetailsRequest extends \App\Http\Requests\Api\BaseRequest
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
            'doctor_code' => 'required|exists:doctors,doctor_code',
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
