<?php

namespace App\Http\Requests\Api\Aggregator\Bookings;

use Illuminate\Validation\Rule;

class CancelAppointmentRequest extends \App\Http\Requests\Api\BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->replace($this->only(['booking_id', 'cancellation_reason']));

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
            'booking_id' => [
                'required',
                Rule::exists('bookings','id')->where(function ($query) {
                    $query->where('booking_source', config('constants.booking_source_internal')['aggregator_'.strtolower($this->header('aggregatorType'))]);
                    $query->whereIn('booking_status', [config('constants.BOOKING.STATUS_INTERNAL')['pending'], config('constants.BOOKING.STATUS_INTERNAL')['confirmed']]);
                })
            ],
            'cancellation_reason' => 'required|in:'.implode(',', array_keys(config('constants.appointment_cancellation_reasons')))
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
            'booking_id.required' => trans('errors.BOOKING_107'),
            'booking_id.exists' => trans('errors.BOOKING_108'),
            'cancellation_reason.required' => trans('errors.BOOKING_109'),
            'cancellation_reason.in' => trans('errors.BOOKING_110', ['values' => implode(',', array_keys(config('constants.appointment_cancellation_reasons')))])
        ];
    }

    public function validate()
    {   
        parent::validate();
        
        $this->request->set('booking_status', config('constants.BOOKING.STATUS_INTERNAL')['canceled']);  // 3 - canceled, Aggreagtors will create confirmed booking 

        if ($this->has('cancellation_reason') && $this->cancellation_reason == 4)
            $this->request->set('cancellation_reason', 9);
    }
}
