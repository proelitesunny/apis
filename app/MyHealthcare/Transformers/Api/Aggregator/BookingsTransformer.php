<?php

namespace App\MyHealthcare\Transformers\Api\Aggregator;

use App\MyHealthcare\Transformers\Api\BaseTransformer;
use App\Models\Hospital;

class BookingsTransformer extends BaseTransformer
{
    public function bookAppointment($booking,$request)
    {
        $response = [];
        $hospital =Hospital::findOrFail($request->hospital_id);
        if($hospital->payment_type==1){
            $response['message'] = trans('success.BOOKING_103');
        }else{
            $bookingId = $booking->id;
            $payment_type = $booking->hospital->payment_type;
            $response['message'] = trans('success.BOOKING_101'); // To Do
            $response['booking_id'] = $bookingId;
            $response['booking_code'] = $booking->booking_code;
            $response['booking_status'] = $booking->getAttributes()['booking_status'];
            $response['fees'] = round($booking->amount, 0);
        }
        
        return $response;
    }

    public function appointmentStatus($booking)
    {
        return [
            'patient_id' => (string)$booking->patient_id,
            'booking_code' => $booking->booking_code,
            'booking_status' => $booking->getAttributes()['booking_status'],
            'fees' => round($booking->amount, 0),
            'uhid' => '',
            'doctor_id' => (string)$booking->doctor_id,
            'doctor_name' => $booking->doctor->name,
            'doctor_speciality_name' => $booking->doctor->doctorSpecialities->implode('name', ','),
            'hospital_id' => (string)$booking->hospital_id,
            'hospital_city_name' => $booking->hospital->city->name,
            'hospital_name' => $booking->hospital->name,
            'hospital_address' => $booking->hospital->address,
            'hospital_primary_contact' => $booking->hospital->primary_contact,
            'appointment_date' => $this->setOutputDateFormat($booking->booking_date),
            'appointment_start_time' => $this->setOutputTimeFormat($booking->start_time),
            'appointment_end_time' => $this->setOutputTimeFormat($booking->end_time)
        ];
    }
}
