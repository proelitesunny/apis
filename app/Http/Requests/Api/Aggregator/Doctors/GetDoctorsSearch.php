<?php

namespace App\Http\Requests\Api\Aggregator\Doctors;

use Illuminate\Foundation\Http\FormRequest;

class GetDoctorsSearch extends FormRequest
{
    
    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

        /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->replace($this->only(['city_name','hospital_name','speciality_name']));
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
            'city_name' => 'nullable',
            'hospital_name' => 'nullable',
            'speciality_name' => 'nullable'
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
            //
        ];
    }
}
