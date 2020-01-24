<?php

namespace App\Http\Requests\Api\Aggregator\Bookings;
use App\MyHealthcare\Validators\Api\Aggregator\PatientsValidator;

class BookAppointmentRequest extends \App\Http\Requests\Api\BaseRequest
{
    public function __construct(
        array $query = array(),
        array $request = array(),
        array $attributes = array(),
        array $cookies = array(),
        array $files = array(),
        array $server = array(),
        $content = null,
        PatientsValidator $patientsValidator)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);

        $patientsValidator->isVerified();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->replace($this->only(['patient_id', 'doctor_id', 'hospital_id', 'booking_date', 'slot_id']));

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
            'patient_id' => 'required|is_verified',
            'doctor_id' => 'required|exists:doctors,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'booking_date' => 'required|date_format:'.config('api.aggregator_api.date_format.input').'|after:yesterday',
            'slot_id' => 'required|exists:doctor_session_slots,id'
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
            'patient_id.required' => trans('errors.PATIENT_147'),
            'patient_id.is_verified' => trans('errors.PATIENT_151'),
            'doctor_id.required' => trans('errors.DOCTOR_109'),
            'doctor_id.exists' => trans('errors.DOCTOR_110'),
            'hospital_id.required' => trans('errors.HOSPITAL_101'),
            'hospital_id.exists' => trans('errors.HOSPITAL_102'),
            'booking_date.required' => trans('errors.BOOKING_101'),
            'booking_date.date_format' => trans('errors.BOOKING_102', ['values' => config('api.aggregator_api.date_format.input')]),
            'booking_date.after' => trans('errors.BOOKING_103'),
            'slot_id.required' => trans('errors.BOOKING_104'),
            'slot_id.exists' => trans('errors.BOOKING_105'),
        ];
    }

    public function validate()
    {   
        parent::validate();
        
        $this->request->set('booking_status', config('constants.BOOKING.STATUS_INTERNAL')['confirmed']);  // 1 - Confiremd, Aggreagtors will create confirmed booking 
        $this->request->set('booking_source', config('constants.booking_source_internal')['aggregator_'.strtolower($this->header('aggregatorType'))]); // Check constants and source.php for aggreagtors types.

        $this->request->set('timeslot_id', $this->slot_id);

        $this->setInternalDateFormat('booking_date');
    }
}
