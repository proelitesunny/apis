<?php

namespace App\Http\Requests\Api\Aggregator\Doctors;

use App\MyHealthcare\Repositories\Booking\BookingInterface;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends \App\Http\Requests\Api\BaseRequest
{
    protected $bookingInterface;
    
    public function __construct(
        array $query = array(),
        array $request = array(),
        array $attributes = array(),
        array $cookies = array(),
        array $files = array(),
        array $server = array(),
        $content = null,
        BookingInterface $booking)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->bookingInterface = $booking;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->replace($this->only(['booking_code', 'booking_codes', 'booking_status']));

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
            /*'booking_code' => 'bail|required_without:booking_codes|exists:bookings,booking_code',
            'booking_codes.*' => 'bail|required_without:booking_code|exists:bookings,booking_code',
            'booking_status' => 'required|in:'.implode(',', array_keys(config('constants.APPOINTMENT_BOOKING'))),*/
            'booking_code' => 'required|exists:bookings,booking_code',
            'cancellation_reason' => 'required|regex:/^[0-9]+$/|in:'.implode(',', array_keys(config('constants.appointment_cancellation_reasons'))),
            'booking_status' => 'required|in:'.implode(',', array_keys(config('constants.APPOINTMENT_BOOKING')))
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
            // 'booking_code.required_without' => trans('errors.BOOKINGS_101'),
            // 'booking_code.exists' => trans('errors.BOOKINGS_102'),
            // 'booking_codes.required_without' => trans('errors.BOOKINGS_101'),
            // 'booking_codes.exists' => trans('errors.BOOKINGS_102')
        ];
    }

    public function validate()
    {
        parent::validate();

        if (isset($this->booking_code)) {
            $booking = $this->bookingInterface->findByCode($this->booking_code);

            $this->offsetUnset('booking_code');
            $this->request->set('booking_id', $booking->id);
            $this->request->set('patient_id', $booking->patient_id);
        }
    }
}
