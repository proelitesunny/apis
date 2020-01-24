<?php

namespace App\MyHealthcare\Repositories\Doctor;

use App\MyHealthcare\Helpers\Slot;
use App\Models\Doctor;
use App\Models\Booking;
use App\Models\Hospital;
use App\Models\Speciality;
use App\Models\DoctorHospital;
use App\Models\HospitalHoliday;
use App\Models\DoctorHoliday;
use App\Models\DoctorSchedule;
use App\Models\BlockDoctorSlot;
use App\Models\DoctorSlotOverriddenFee;

class DoctorRepository implements DoctorInterface {

    private $slot;
    private $doctorsList;
    private $doctorSchedule;

    public function __construct(Doctor $doctor, Slot $slot, DoctorSchedule $doctorSchedule) {
        $this->doctor = $doctor;
        $this->slot = $slot;
        $this->doctorSchedule = $doctorSchedule;
    }

    public function find($id) {
        $doctor = $this->doctor->with('user', 'doctorSpecialities', 'doctorHospital')->findOrFail($id);
        return $doctor;
    }

    public function getAppointments($params) {
        try {
            $start_date = config('api.aggregator_api.bookings.start_date');
            $end_date = config('api.aggregator_api.bookings.end_date');

            $bookingSource = config('constants.booking_source_internal')['aggregator_'.strtolower(request()->header('aggregatorType'))];

            $bookingStatus = [
                config('constants.BOOKING.STATUS_INTERNAL.confirmed'),
                config('constants.BOOKING.STATUS_INTERNAL.canceled')
            ]; // Need to take from constants.
            $pagination = config('api.aggregator_api.appointments_pagination');
            $patientId = 0;
            $addDays = config('api.aggregator_api.bookings.appointments_days');

            if (!empty($params['start_date'])) {
                $start_date = \Carbon\Carbon::parse($params['start_date'])->format('Y-m-d');
                $end_date = \Carbon\Carbon::parse($start_date)->addDay($addDays)->format('Y-m-d');
            }
            if (!empty($params['end_date'])) {
                if (empty($params['start_date'])) {
                    abort(400, trans('errors.APPOINTMENT_102'));
                }
                $end_date = \Carbon\Carbon::parse($params['end_date'])->format('Y-m-d');
            }

            $passedDataAction['start_date'] = $start_date;
            $passedDataAction['end_date'] = $end_date;
            if (!empty($params['patient_id'])) {
                $patientId = $params['patient_id'];
                $passedDataAction['patient_id'] = $patientId;
            }

            $start_date = \Carbon\Carbon::parse($start_date)->startOfDay()->format('Y-m-d H:i:s');
            $end_date = \Carbon\Carbon::parse($end_date)->endOfDay()->format('Y-m-d H:i:s');

            $details = Booking::whereHas('patient', function($query) {
                        $query->where('is_active',1);
                    })
                    ->whereHas('hospital', function($query) {

                    })
                    ->whereHas('doctor', function($query) {
                        $query->where('is_active_doctor',1);
                    })
                    ->with(array('patient.patientHasManyHisMapping' => function($query) {

                        }))
                    ->whereBetween('booking_date', [$start_date, $end_date])
                    ->whereIn('booking_status', $bookingStatus)
                    ->where('booking_source', $bookingSource);
            if (!empty($patientId)) {
                $details->where('patient_id', $patientId);
            }
            $details = $details->paginate($pagination);

            $finalData = $details->transform(function($result) {
                if ((isset($result->patient->patientHasManyHisMapping)) && (!$result->patient->patientHasManyHisMapping->isEmpty())) {
                    $result->patient->patientHasManyHisMapping = $result->patient->patientHasManyHisMapping->filter(function($q) use($result) {
                        return $q->fortis_hospital_code == $result->hospital->fortis_hospital_code;
                    });
                } else {
                    $result->patient->patientHasManyHisMapping = null;
                }
                return $result;
            });

            $pagination = $this->generatePaginationDetails($details, $passedDataAction);
            $response['pagination'] = $pagination;
            $response['data'] = $finalData;

            return $response;
        } catch (\Exception $ex) {
            abort(400, $ex->getMessage());
        }
    }

    public function generatePaginationDetails($details, $passedDataAction) {
        $prev = NULL;
        $next = NULL;

        if (!empty($details->previousPageUrl())) {
            $prev = $details->previousPageUrl();
            if (isset($passedDataAction['start_date'])) {
                $prev = $prev . '&start_date=' . $passedDataAction['start_date'];
            }
            if (isset($passedDataAction['end_date'])) {
                $prev = $prev . '&end_date=' . $passedDataAction['end_date'];
            }
            if (isset($passedDataAction['patient_id'])) {
                $prev = $prev . '&patient_id=' . $passedDataAction['patient_id'];
            }
        }

        if (!empty($details->nextPageUrl())) {
            $next = $details->nextPageUrl();
            if (isset($passedDataAction['start_date'])) {
                $next = $next . '&start_date=' . $passedDataAction['start_date'];
            }
            if (isset($passedDataAction['end_date'])) {
                $next = $next . '&end_date=' . $passedDataAction['end_date'];
            }
            if (isset($passedDataAction['patient_id'])) {
                $next = $next . '&patient_id=' . $passedDataAction['patient_id'];
            }
        }

        $link = ['prev' => $prev, 'next' => $next];
        $responseArray = [
            'total' => $details->total(),
            'per_page' => $details->perPage(),
            'current_page' => $details->currentPage(),
            'total_pages' => $details->lastPage(),
            'links' => $link,
        ];
        return $responseArray;
    }

    public function getDoctorsSearch($params) {
        try {

            $pagination = config('api.aggregator_api.appointments_pagination');
            $hospitalId = array();
            $specialityId = array();
            $cityName = "";

            $hospitalName = "";
            if (!empty($params['hospital_name'])) {
                $hospitalName = $params['hospital_name'];
            }
            $specialityName = "";
            if (!empty($params['speciality_name'])) {
                $specialityName = $params['speciality_name'];
            }
            $cityName = "";
            if (!empty($params['city_name'])) {
                $cityName = $params['city_name'];
            }

            $details = DoctorHospital::whereHas('doctor', function($query) {
                       $query->where('is_active_doctor',1);
                    })->whereHas('hospital', function($query) use ($hospitalName) {
                        if (!empty($hospitalName)) {
                            $query->where('name', 'like', '%' . $hospitalName . '%');
                        }
                    });

            if (!empty($specialityName))
            {
                $details = $details->whereHas('doctor.doctorSpecialities',function($query) use($specialityName) {
                    $query->where('name', 'like', '%' . $specialityName . '%');
                });
            }
            if (!empty($cityName))
            {
                $details = $details->whereHas('hospital.city',function($query) use($cityName) {
                    $query->where('name', 'like', '%' . $cityName . '%');
                });
            }
            /*
                        ->with(array('doctor.city' => function($query) use ($cityName) {
                            if (!empty($cityName)) {
                                $query->where('name', 'like', '%' . $cityName . '%');
                            }
                        }))->paginate($pagination);

            */
            $details = $details->paginate($pagination);


            $returnData['details'] = $details;
            $returnData['hospital_name'] = $hospitalName;
            $returnData['speciality_name'] = $specialityName;
            $returnData['city_name'] = $cityName;



            return $returnData;
        } catch (\Exception $ex) {
            abort(400, $ex->getMessage());
        }
    }

    public function getDoctorsAvailableSlots($params) {
        try {
            $slotDates = array();

            $doctorId = $params['doctor_id'];
            $hospitalId = $params['hospital_id'];
            $date = $params['appointment_date'];
            $includeDoctor = FALSE;
            if(!empty($params['include']) && ($params['include'] == 'doctor')) {
                $includeDoctor = TRUE;
            }

            $response['doctor_id'] = $doctorId;
            $response['hospital_id'] = $hospitalId;
            $response['appointment_date'] = $date;
            $response['include_doctor'] = $includeDoctor;
            $response['doctor_details'] = array();
            $response['slot_details'] = array();

            $hospital = Hospital::select('id','buffer_time')->find($hospitalId);
            $defaultTime = config("api.default_origin_date")." ".$hospital->buffer_time;
            $originTime = config("api.default_origin_date")." 00:00:00";
            $bufferTime = strtotime($defaultTime)-strtotime($originTime);
            $time = \Carbon\Carbon::now()->addMinutes(330)->format("H:i:s");
            $endTime = date("H:i:s",strtotime(config("api.default_origin_date")." ".$time) + $bufferTime);

            if($this->checkIfHospitalHoliday($hospitalId, $date)) {
                abort(400, trans('errors.SLOT_102'));
            }
            if($this->checkIfDoctorHoliday($doctorId, $date)) {
                abort(400, trans('errors.SLOT_103'));
            }

            if($this->checkIfHospitalSoftDeleted($doctorId, $hospitalId)){
                abort(400, trans('errors.HOSPITAL_103'));
            }

            if(!(Doctor::where(array('id'=>$doctorId,'is_active_doctor'=>1))->exists())) {
                abort(400, trans('errors.SLOT_104'));
            }

            /*if ($this->checkIfHospitalHoliday($hospitalId, $date) || $this->checkIfDoctorHoliday($doctorId, $date)) {
                abort(400, "Hospital Holiday");
            }*/

            $dayArray = array_flip(config('constants.WEEK_DAYS_ENUM'));
            $dayOfWeek = $dayArray[date("l", strtotime($date))];
            $defaultAppointmentDuration = config('constants.DEFAULT_APPOINTMENT_DURATION');

            $doctorSchedulesDetails = $this->doctorSchedule->with(['sessions' => function ($query) use ($dayOfWeek) {
                            $query->where('day_of_week', $dayOfWeek);
                        }])->whereHas('sessions.slots', function ($query) {
                        $query->where('visibility_c', 1);
                    })->where('doctor_id', $doctorId)
                    ->where('hospital_id', $hospitalId)
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->get();


            if (isset($doctorSchedulesDetails) && (!empty($doctorSchedulesDetails->toArray()))) {

                if((isset($doctorSchedulesDetails[0]->doctor)) && (!empty($doctorSchedulesDetails[0]->doctor->toArray())) && ($includeDoctor == TRUE)) {
                    $response['doctor_details'] = $this->getDoctorDetailsAvailableSlots($doctorSchedulesDetails[0]->doctor->id,$hospitalId);
                }

                foreach ($doctorSchedulesDetails as $doctorSchedule) {
                    if (!empty($doctorSchedule->sessions->toArray())) {
                        foreach ($doctorSchedule->sessions as $session) {
                            if (strtotime($date) == strtotime(date("Y-m-d"))) {
                                //$availableSlots = $session->slots()->where('visibility_c', 1)->where('time', '>=', date("H:i:s", strtotime('+330 minutes')))->get();
                                $availableSlots = $session->slots()->where('visibility_c', 1)->where('time', '>=', $endTime)->get();
                            } else {
                                $availableSlots = $session->slots()->where('visibility_c', 1)->get();
                            }

                            foreach ($availableSlots as $slot) {
                                $isAvailable = 1;
                                $isBlocked = false;
//                                if (Booking::where('booking_date', $date)->where('doctor_session_slot_id', $slot->id)->where('booking_status','!=',3)->whereNotNull('rescheduled_against')->first()) {
//                                    $isAvailable = 0;
//                                }
                                if (Booking::where('booking_date', $date)->where('doctor_session_slot_id', $slot->id)->where(function($query){
                                    $query->where('booking_status', config('constants.BOOKING.STATUS_INTERNAL')['confirmed']);
                                    $query->orWhere('booking_status', config('constants.BOOKING.STATUS_INTERNAL')['pending']);
                                })->first()) {
                                    $isAvailable = 0;
                                }
                                if (BlockDoctorSlot::where('block_date', $date)->where('doctor_session_slot_id', $slot->id)->where('is_blocked', true)->first()) {
                                    $isBlocked = true;
                                    $isAvailable = 0;
                                }

                                if($slot->visibility_c == 0){
                                    $isAvailable = 0;
                                }
                                $startTime = date("H:i", strtotime($slot->time));
                                $endTime = date("H:i", strtotime('+' . $defaultAppointmentDuration . ' minutes', strtotime($startTime)));
                                if (($session->override_appointment_duration == 1) && ($session->overridden_appointment_duration != NULL)) {
                                    $appointmentTime = $session->overridden_appointment_duration;
                                    $endTime = date("H:i", strtotime('+' . $appointmentTime . ' minutes', strtotime($startTime)));
                                }

                                $fees = $session->overridden_fees;
                                if (($session->override_fees == 0) && ($session->overridden_fees == NULL)) {
                                    $feesDetails = DoctorHospital::select('fees')->where(array('doctor_id' => $doctorId, 'hospital_id' => $hospitalId))->first();
                                    if (isset($feesDetails) && (!empty($feesDetails->toArray()))) {
                                        $fees = $feesDetails->fees;
                                    }
                                }

                                if (DoctorSlotOverriddenFee::where('doctor_session_slots_id', $slot->id)->first()) {
                                    $newlyOverriddenFees = DoctorSlotOverriddenFee::select('overridden_fees', 'schedule_date')->where("schedule_date", $date)->where('doctor_session_slots_id', $slot->id)->first();
                                    if (empty($newlyOverriddenFees)) {
                                        $slotDates['slots'][] = [
                                            'id' => $slot->id,
                                            'start_time' => $startTime,
                                            'end_time' => $endTime,
                                            'fees' => (!empty($fees)) ? $fees : "",
                                            'is_available' => $isAvailable,
                                        ];
                                    }
                                } else {
                                    $slotDates['slots'][] = [
                                        'id' => $slot->id,
                                        'start_time' => $startTime,
                                        'end_time' => $endTime,
                                        'fees' => (!empty($fees)) ? $fees : "",
                                        'is_available' => $isAvailable,
                                    ];
                                }
                            }
                        }
                    }
                }
            }

            $response['slot_details'] = $slotDates;
            return $response;
        } catch (\Exception $ex) {
            abort(400, $ex->getMessage());
        }

    }

    public function checkIfHospitalHoliday($hospitalId, $date) {
        if (HospitalHoliday::whereHas('hospitals', function ($query) use ($hospitalId) {
                    $query->where('hospital_id', $hospitalId);
                })->where('date', $date)->first()) {
            return true;
        }

        return false;
    }

    public function checkIfDoctorHoliday($doctorId, $date) {
        if (DoctorHoliday::where('doctor_id', $doctorId)
                        ->where('start_date', '<=', $date)
                        ->where('end_date', '>=', $date)
                        ->first()
        ) {
            return true;
        }
        return false;
    }

    public function checkIfHospitalSoftDeleted($doctorId, $hospitalId){
        if(DoctorHospital::onlyTrashed()->whereDoctorId($doctorId)->whereHospitalId($hospitalId)->first()){
            return true;
        }
    }

    public function getDoctorDetailsAvailableSlots($doctor_id,$hospital_id) {
        return DoctorHospital::whereHas('doctor',function($query){

        })->whereHas('hospital', function($query) {

        })->where(array('doctor_id'=>$doctor_id,'hospital_id'=>$hospital_id))->first();
    }

}
