<?php

namespace App\MyHealthcare\Repositories\Booking;

use App\MyHealthcare\Helpers\GenerateCode;
use App\MyHealthcare\Helpers\Slot;
use App\MyHealthcare\Repositories\Doctor\DoctorInterface;
use App\MyHealthcare\Repositories\DoctorSessionSlot\DoctorSessionSlotInterface;
use App\MyHealthcare\Repositories\Transaction\TransactionInterface;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use App\Models\Hospital;
use App\Models\DoctorSessionSlot;

class BookingRepository implements BookingInterface
{
	/**
	 * @var Booking
	 */
	private $booking;

    /**
     * @var GenerateCode
     */
	private $generateCode;

    /**
     * @var Slot
     */
	private $slot;

    /**
     * @var PatientInterface
     */
	private $patient;

    /**
     * @var DoctorInterface
     */
	private  $doctor;


  private $doctorSessionSlot;


  private $transaction;

    /**
     * BookingRepository constructor.
     * @param Booking $booking
     * @param Slot $slot
     * @param GenerateCode $generateCode
     * @param DoctorInterface $doctor
     * @param DoctorSessionSlotInterface $doctorSessionSlot
     */
	public function __construct(
	    Booking $booking,
        Slot $slot,
        GenerateCode $generateCode,
        DoctorInterface $doctor,
        DoctorSessionSlotInterface $doctorSessionSlot,
        TransactionInterface $transaction
    ) {
		$this->booking = $booking;
		$this->slot = $slot;
		$this->generateCode = $generateCode;
		$this->doctor = $doctor;
    $this->doctorSessionSlot = $doctorSessionSlot;
    $this->transaction = $transaction;
	}

    public function store($patientId, $doctorId, $request)
    {
        $hospital =Hospital::findOrFail($request->hospital_id);
        if($hospital->payment_type==1){
            return true;
        }
        DB::beginTransaction();

        $this->checkIfHospitalHoliday($request->input('hospital_id'), $request->input('booking_date'));
        $this->checkIfDoctorHoliday($request->input('doctor_id'), $request->input('booking_date'));

        $booking = $this->booking->newInstance();

        $slot = $this->doctorSessionSlot->getSessionBy($request->input('timeslot_id'));

        $duration = config('constants.DEFAULT_APPOINTMENT_DURATION');

        if ($slot->doctorScheduleSession->override_appointment_duration) {
            $duration = $slot->doctorScheduleSession->overridden_appointment_duration;
        }

        $endTime = date("H:i", strtotime('+'.$duration.' minutes', strtotime($slot->time)));

        $this->checkHospitalBufferTime($request,$slot);
        
        $this->checkIfPatientBookingExistsOnSameTime($patientId, $request, $booking, $slot, $endTime);

        $this->checkIfSlotBlocked($request);

        $this->checkIfBookingExists($request);

        $booking->patient_id = $patientId;

        $booking->doctor_id = $doctorId;

        $booking->hospital_id = $request->input('hospital_id');

        $booking->booking_date = $request->input('booking_date');

        $booking->doctor_session_slot_id = $request->input('timeslot_id');

        $discount = 0;

        $fees = $this->getFees($doctorId, $request->input('timeslot_id'), $request->input('hospital_id'));

        $booking->amount = $fees;

        $booking->convenience_fees = config('constants.CONVEYANCE_FEES');

        $booking->discount_amount = $discount;

        $totalAmount = $this->getTotalAmount($fees, config('constants.CONVEYANCE_FEES'), $discount);

        $booking->total_amount = $totalAmount;

        $booking->start_time = $slot->time;

        $booking->end_time = $endTime;

        if ($request->has('booking_source')) {
            $booking->booking_source = $request->booking_source;
        }

        if ($request->has('booking_status')) {
            $booking->booking_status = $request->booking_status;
        }

        $booking->save();

        $booking = $this->booking->find($booking->id);

        // Make Entry in transaction table
        $patientId = $booking->patient->id;
        $this->transaction->create('cash', $booking->total_amount, $booking->id, $patientId, config('constants.PAYMENT_STATUS_INTERNAL')['pending'], null, null);

        DB::commit();

        return $booking;
    }

    public function find($id)
    {
        return $this->booking->with('patient', 'doctor')->findOrFail($id);
    }

    public function updateAppointmentStatus($request)
    {
        if($request->has('cancellation_reason')) {
            $isUpdated = $this->booking->where('id', $request->booking_id)->update(['booking_status' => $request->booking_status, 'cancellation_reason' => $request->cancellation_reason]);
        } else {
            $isUpdated = $this->booking->where('id', $request->booking_id)->update(['booking_status' => $request->booking_status]);
        }

        if (!$isUpdated) {
          abort(400, trans('errors.BOOKING_111'), es('booking_id'));
        }

        return $isUpdated;
    }

    private function getFees($doctorId, $slotId, $hospitalId)
    {
        $fees = 0;

        $slot = $this->doctorSessionSlot->getSessionBy($slotId);

        $doctor = $this->doctor->find($doctorId);

        foreach ($doctor->doctorHospital as $hospital) {
            if ($hospital->id == $hospitalId) {
                $fees = $hospital->pivot->fees;
                break;
            }
        }

        if ($slot->doctorScheduleSession->override_fees) {
            $fees = $slot->doctorScheduleSession->overridden_fees;
        }

        if (!$fees) {
            throw new \App\Exceptions\FeesException("Doctor or session fees not present");
        }

        return $fees;
    }

    private function getTotalAmount($fees, $convinenceFess, $discount)
    {
        return $fees + $convinenceFess - $discount;
    }

    /**
     * @param $patientId
     * @param $request
     * @param $booking
     * @param $slot
     * @param $endTime
     * @throws BookingException
     */
    private function checkIfPatientBookingExistsOnSameTime($patientId, $request, $booking, $slot, $endTime)
    {
        if ($existingBookings = $booking->where('booking_date', $request->input('booking_date'))
            ->where('booking_status', config('constants.BOOKING.STATUS_INTERNAL')['confirmed'])
            ->where('patient_id', $patientId)
            ->get()
        ) {
            foreach ($existingBookings as $existingBooking) {
                if ((strtotime($slot->time) > strtotime($existingBooking->start_time) && strtotime($slot->time) < strtotime($existingBooking->end_time)) || (strtotime($endTime) > strtotime($existingBooking->start_time) && strtotime($endTime) <= strtotime($existingBooking->end_time)) || (strtotime($slot->time) < strtotime($existingBooking->start_time) && strtotime($endTime) >= strtotime($existingBooking->end_time))) {
                    throw(new \App\Exceptions\BookingException("Booking already exists for same date and time"));
                }
            }
        }
    }

    /**
     * @param $request
     * @throws BookingException
     */
    private function checkIfSlotBlocked($request)
    {
        if (\App\Models\BlockDoctorSlot::where('block_date', $request->input('booking_date'))
            ->where('doctor_session_slot_id', $request->input('timeslot_id'))
            ->where('is_blocked', 1)
            ->first()
        ) {
            throw(new \App\Exceptions\BookingException("Doctor Slot is Blocked"));
        }
    }

    /**
     * @param $request
     * @throws BookingException
     */
    private function checkIfBookingExists($request)
    {
        if (Booking::where('booking_date', $request->input('booking_date'))->where('doctor_session_slot_id', $request->input('timeslot_id'))->where(function($query){
                            $query->where('booking_status', config('constants.BOOKING.STATUS_INTERNAL')['confirmed']);
                            $query->orWhere(function($q) {
                                $q->where('booking_status', config('constants.BOOKING.STATUS_INTERNAL')['pending']);
                                $q->where('created_at', '>', config('api.aggregator_api.bookings.blocked_time'));
                            });
                        })->first()) {
            throw(new \App\Exceptions\BookingException("Booking already exists"));
        }
    }

    /**
     * @param $hospitalId
     * @param $date
     * @throws BookingException
     */
    private function checkIfHospitalHoliday($hospitalId, $date)
    {
        if (\App\Models\HospitalHoliday::whereHas('hospitals', function ($query) use ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        })->where('date', $date)->first()) {
            throw(new \App\Exceptions\BookingException("Hospital Holiday exists on date."));
        }
    }

    /**
     * @param $doctorId
     * @param $date
     * @throws BookingException
     */
    private function checkIfDoctorHoliday($doctorId, $date)
    {
        if (\App\Models\DoctorHoliday::where('doctor_id', $doctorId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first()
        ) {
            throw(new \App\Exceptions\BookingException("Doctor Holiday exists on date."));
        }
    }
    
    private function checkHospitalBufferTime($request, $slot){
        $hospital = Hospital::select('id','buffer_time')->find($request->hospital_id);
        $defaultTime = config("api.default_origin_date")." ".$hospital->buffer_time;
        $originTime = config("api.default_origin_date")." 00:00:00";
        $bufferTime = strtotime($defaultTime)-strtotime($originTime);
        $time = \Carbon\Carbon::now()->addMinutes(330)->format("H:i:s");
        $endTime = date("H:i:s",strtotime(config("api.default_origin_date")." ".$time) + $bufferTime);
        
        if(strtotime($request->booking_date) == strtotime(date("Y-m-d")) ){
            $bufferTimeSlot = DoctorSessionSlot::where('id',$slot->id)->where('visibility_a',1)->where('time','<=',$endTime)->count();
            if($bufferTimeSlot > 0){
                throw new \Exception("Slot is not available",400);
            }
        }
    }
}
