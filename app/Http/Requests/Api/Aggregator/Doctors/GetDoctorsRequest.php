<?php

namespace App\Http\Requests\Api\Aggregator\Doctors;

class GetDoctorsRequest extends \App\Http\Requests\Api\BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->replace($this->only(['keyword']));
        
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
            'keyword' => 'min:3|max:20'
        ];
    }
}
