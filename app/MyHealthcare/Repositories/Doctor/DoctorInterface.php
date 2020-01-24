<?php

namespace App\MyHealthcare\Repositories\Doctor;

interface DoctorInterface
{
    public function find($id);
    
    public function getAppointments($params);
    
    public function generatePaginationDetails($details, $passedDataAction);
    
    public function getDoctorsSearch($params);
    
    public function getDoctorsAvailableSlots($params);
    
    public function checkIfHospitalHoliday($hospitalId, $date);
    
    public function checkIfDoctorHoliday($doctorId, $date);
    
    public function getDoctorDetailsAvailableSlots($doctor_id,$hospital_id);
}
