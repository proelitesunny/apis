<?php

namespace App\Http\Controllers\Api\Aggregator;

use Illuminate\Http\Request;
use App\Http\Requests\Api\Aggregator\Bookings\BookAppointmentRequest;
use App\Http\Requests\Api\Aggregator\Bookings\CancelAppointmentRequest;

use App\MyHealthcare\Repositories\Booking\BookingInterface;
use App\MyHealthcare\Transformers\Api\Aggregator\BookingsTransformer;
use App\Models\Hospital;

class BookingsController extends BaseController
{
    function __construct(
    	BookingInterface $booking,
    	BookingsTransformer $bookingsTransformer
	)
    {
		$this->booking = $booking;
		$this->bookingsTransformer = $bookingsTransformer;
    }

    public function bookAppointment(BookAppointmentRequest $request)
    {
        try {
            $booking = $this->booking->store($request->patient_id, $request->doctor_id, $request);
        }
        catch(\App\Exceptions\BookingException $e) {
            abort(400, $e->getMessage());
        }
        catch(\App\Exceptions\FeesException $e) {
            abort(400, $e->getMessage());
        }
        catch(\Exception $e) {
            logger()->error($e->getMessage());
            //abort(400, trans('errors.BOOKING_106'));
            if($e->getCode()=='400'){
                abort(400, $e->getMessage());
            }else{
                abort(400, trans('errors.BOOKING_106'));
            }
        }

        $response = $this->bookingsTransformer->bookAppointment($booking, $request);

        return response()->json($response, 200);
    }

    public function appointmentStatus(Request $request, $bookingId)
    {
    	$booking = $this->booking->find($bookingId);

    	$response = $this->bookingsTransformer->appointmentStatus($booking);
        return response()->json($response, 200);
    }

    public function cancelAppointment(CancelAppointmentRequest $request)
    {
        $this->booking->updateAppointmentStatus($request);

        return response()->json([
                    'message' => trans('success.BOOKING_102'),
                ], 200);
    }
}
