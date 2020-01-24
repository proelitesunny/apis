<?php

namespace App\Http\Controllers\Api\Aggregator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MyHealthcare\Repositories\TestBooking\TestBookingInterface;
use App\MyHealthcare\Transformers\Api\Aggregator\V1\TestBookingTransformer;

class TestBookingController extends BaseController {

    public function getAppointments(Request $request, TestBookingTransformer $testBookingTransformer, TestBookingInterface $testBookingInterface)
    {
        return $testBookingTransformer->getTestAppointmentDetailsResponse(
            $testBookingInterface->getTestAppointments(
                    $request->only(
                    ['appointment_date', 'modified_date', 'page', 'test_booking_code','from_datetime','to_datetime']
                )
                )
        );
    }

}
