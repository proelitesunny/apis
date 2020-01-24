<?php

namespace App\Http\Controllers\Api\Aggregator;

use Illuminate\Http\Request;
use App\MyHealthcare\Repositories\Doctor\DoctorInterface;
use App\MyHealthcare\Transformers\Api\Aggregator\DoctorsTransformer;
use App\Http\Requests\Api\Aggregator\Doctors\GetAppointments;
use App\Http\Requests\Api\Aggregator\Doctors\GetDoctorsSearch;
use App\Http\Requests\Api\Aggregator\Doctors\GetDoctorsAvailableSlots;

class DoctorsController extends BaseController {

    protected $doctorsTransformer;
    protected $doctor;

    function __construct(
    DoctorInterface $doctor, DoctorsTransformer $doctorsTransformer
    ) {
        $this->doctor = $doctor;
        $this->doctorsTransformer = $doctorsTransformer;
    }

    public function appointments(GetAppointments $request) {
        return $this->doctorsTransformer->getAppointmentDetail(
                        $this->doctor->getAppointments(
                                $request->only([
                                    'start_date', 'end_date', 'patient_id'
                                ])
        ));
    }
    
    public function doctorsSearch(GetDoctorsSearch $request) {
        return $this->doctorsTransformer->getDoctorsDetail(
                        $this->doctor->getDoctorsSearch(
                                $request->only([
                                    'city_name', 'hospital_name', 'speciality_name'
                                ])
        ));
    }
    
    public function availableSlots(GetDoctorsAvailableSlots $request) {
        return $this->doctorsTransformer->getDoctorsAvailableSlotDetail(
                        $this->doctor->getDoctorsAvailableSlots(
                                $request->only([
                                    'doctor_id','hospital_id','appointment_date','include'
                                ])
        ));
    }

}
