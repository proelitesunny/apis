<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestBooking extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at','checkup_date'];

    public function test()
    {
        //dd("s");
        return $this->belongsTo(Test::class, "test_id");
    }

    public function patient()
    {
        //dd("hh");
        return $this->belongsTo(Patient::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    //getBookingDateTimeAttribute
    public function getBookedByDisplayAttribute()
    {
        $patient = AdminUser::find($this->created_by);
        if ($patient->first_name) {
            return $patient->first_name;
        } else {
            return "--";
        }
    }

    public function coupon_code()
    {
        return $this->belongsTo(CouponCode::class);
    }

    public function getBookedByDateTimeAttribute()
    {

    }

    public function transactionLatestFirst()
    {
        if ($this->transactions()->where('payment_status', config('constants.PAYMENT_STATUS_INTERNAL')['success'])->latest()->first()) {
            return $this->transactions()->where('payment_status', config('constants.PAYMENT_STATUS_INTERNAL')['success'])->latest()->first();
        } elseif ($this->transactions()->where('payment_type', config('constants.PAYMENT_MODE_INTERNAL')['cash'])->latest()->first()) {
            return $this->transactions()->where('payment_type', config('constants.PAYMENT_MODE_INTERNAL')['cash'])->latest()->first();
        }
        return $this->transactions()->latest()->first();
    }

    public function transaction()
    {
        //dd($this->transactions
        if ($this->transactions()->where('payment_status', config('constants.PAYMENT_STATUS_INTERNAL')['success'])->latest()->first()) {
            return $this->transactions()->where('payment_status', config('constants.PAYMENT_STATUS_INTERNAL')['success'])->latest()->first();
        } elseif ($this->transactions()->where('payment_type', config('constants.PAYMENT_MODE_INTERNAL')['cash'])->latest()->first()) {
            return $this->transactions()->where('payment_type', config('constants.PAYMENT_MODE_INTERNAL')['cash'])->latest()->first();
        }
        return $this->transactions()->latest()->first();
    }

    public function transactions()
    {
        return $this->hasMany(TestTransaction::class, "test_booking_id");
    }

    public function txnPatientId($booking)
    {
        $patient = $booking->patient;
        $patientId = (empty($patient->parent) || $patient->is_family_member == 0) ? $patient->id : $patient->parent->id;
        return $patientId;
    }

    public function getCheckStatusBookingBasedOnTimeAttribute()
    {
        $date = $this->checkup_date->format('Y-m-d').' '.date("H:i:s", strtotime($this->mappedTest->end_time));

        if (strtotime($date) < strtotime('+270 minutes')) {
            return 'Missed';
        }

        return config('constants.BOOKING.STATUS')[$this->booking_status];
    }

    public function getStatusBookingAttribute($value)
    {
        $date = $this->checkup_date->format('Y-m-d').' '.date("H:i:s", strtotime($this->mappedTest->end_time));
        if ($this->booking_status == 1 && !$this->is_patient_arrived && strtotime($date) < strtotime('+270 minutes')) {
            return 'Missed';
        } elseif ($this->booking_status == 1 && $this->is_patient_arrived) {
            return 'Completed';
        }

        return config('constants.BOOKING.STATUS')[$this->booking_status];
    }

    public function updateAppointmentStatus($request)
    {

        $booking = self::find($request->test_booking_id);

        $booking->booking_status = $request->booking_status;

        $booking->booking_time = $request->booking_time;

        if ($request->has('cancellation_reason')) {
            $booking->cancellation_reason = $request->cancellation_reason;
        }

        if (!$booking->save()) {
            abort(400, trans('errors.BOOKINGS_103'), es('booking_id'));
        }

        return $booking;
    }

    public function getPatientUhidDisplayAttribute()
    {
        $patientMappings = $this->patient->patientHisMapping;
        foreach ($patientMappings as $patientMapping) {
            if ($patientMapping->woodlands_hospital_code == $this->hospital->woodlands_hospital_code) {
                return $patientMapping->woodlands_uhid;
            }
        }

        return 'N/A';
    }

    public function mappedTest()
    {
        return $this->belongsTo(TestHospitalMapping::class, "test_hospital_mapping_id");
    }

    public function patientHisMapping()
    {
        return $this->hasOne(patientHisMapping::class, "patient_id", "patient_id");
    }
    public function testRefund()
    {
        return $this->hasOne(TestRefund::class, "test_booking_id");
    }

}
