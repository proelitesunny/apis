<?php

namespace App\MyHealthcare\Transformers\Api\Aggregator;

use App\MyHealthcare\Transformers\Api\BaseTransformer;

class PatientsTransformer extends BaseTransformer
{
	protected $doctorsTransformer;

	function __construct(DoctorsTransformer $doctorsTransformer)
	{
	    $this->doctorsTransformer = $doctorsTransformer;
	}


    public function getAllAppointments($appointments)
    {
        $response = [];

        $response['appointments'] = $appointments->transform(function($appointment) {

            return $this->getAppointment($appointment);
        });

        $response['pagination'] = [
            'total' => $appointments->total(),
            'per_page' => $appointments->perPage(),
            'current_page' => $appointments->currentPage(),
            'total_pages' => $appointments->lastPage(),
            'links' => [
                'next' => $this->nextPageUrl($this->getBaseUri(), $appointments->nextPageUrl()),
            ]
        ];

        return $response;
    }

    public function getAppointment($appointment)
    {
        $doctorsTransformer = $this->doctorsTransformer;

        $paymentStatus = ($appointment->transaction->payment_status == 1)?'paid':'pending';
            $bookingDate = \Carbon\Carbon::parse($appointment->booking_date.' '.$appointment->booking_time);

        return [
            'id' => $appointment->id,
            'booking_id' => $appointment->booking_code,
            'booking_status' => $appointment->status_booking,
            'payment_status' => $paymentStatus,
            'amount' => $paymentStatus == 'paid'? round($appointment->transaction->amount, 0) : round($appointment->amount, 0),
            'booking_date' => $bookingDate->format('D, d M h:i A'),
            'doctor' => $doctorsTransformer->getDoctor($appointment->doctor)
        ];
    }
}