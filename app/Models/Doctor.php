<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Doctor extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    public function doctorSpecialities()
    {
        return $this->belongsToMany(Speciality::class, 'doctor_speciality');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctorHospital()
    {
        return $this->belongsToMany(Hospital::class, 'doctor_hospital')->whereNull('doctor_hospital.deleted_at')->withPivot('fees');
    }
    
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
    
//    public function doctorHospitalMapping()
//    {
//        return $this->belongsToMany(Hospital::class, 'doctor_hospital')->whereNull('doctor_hospital.deleted_at')->withPivot(array('fees','designation', 'slot_duration'));
//    }

    public function getNameAttribute()
    {
        return 'Dr. ' . ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    /*protected $casts = [
        'qualification' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    

    public function doctorFacility()
    {
        return $this->belongsToMany(Facility::class, 'doctor_facility');
    }

    public function doctorSchedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function doctorTimeSchedule()
    {
        return $this->hasMany(DoctorTimeSchedule::class);
    }

    public function doctorHospital()
    {
        return $this->belongsToMany(Hospital::class, 'doctor_hospital')->whereNull('doctor_hospital.deleted_at');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
    public function getScheduleDaysOfWeekAttribute()
    {
        $doctorTimeSchedules = $this->doctorTimeSchedule;
        $daysOfWeeks = [];
        
        if(!empty($doctorTimeSchedules)) {
            foreach($doctorTimeSchedules as $schedule) {
                $daysOfWeeks[] = config('constants.WEEK_DAYS_ENUM')[$schedule->day_of_week];
            }
        }
       
        return array_unique($daysOfWeeks);
    }

    public function getGenderAttribute($value)
    {
          return config('constants.GENDER_ENUM')[$value];
    }

    public function getNameAttribute()
    {
        return 'Dr. ' . ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function getProfilePictureAttribute($value) {    

        $profile_pic = config('constants.upload_path').$value;
        
        if(file_exists($profile_pic) && is_file($profile_pic)){
            
            return Asset::getImagePath($value);

        }else{
            return url('/').'/assets/images/user.png';
        }

    }
    
    public function starDoctorAssistants(){
        //StarDoctorAssistant
        return $this->belongsToMany(StarDoctorAssistant::class);
    }
    
    
    public function getDoctorSpecialityListAttribute(){
        $specialityList = [];
        
        if(isset($this->doctorSpecialities) && $this->doctorSpecialities->count()>0){
            foreach($this->doctorSpecialities as $speciality){
                $specialityList[]=$speciality->name;
            }
        }
        
        return $specialityList;
    }
    
    public function getDoctorHospitalListAttribute(){
        $hospitalList = [];
        
        if(isset($this->doctorHospital) && $this->doctorHospital->count()>0){
            foreach($this->doctorHospital as $hospital){
                $hospitalList[$hospital->id]=$hospital->name;
            }
        }
        
        return $hospitalList;
    }
    
    public function getDoctorRevisitingChargeObjAttribute(){
        if(isset($this->doctorRevisitingCharge) && !empty($this->doctorRevisitingCharge)){
            return $this->doctorRevisitingCharge;
        }else{
            $doctorRevisitingChargeConfig = Configuration::where('configuration_key','doctor_revisiting_charges')->first();            
            if(!empty($doctorRevisitingChargeConfig)){
                $doctorRevisitingChargeObj = new DoctorRevisitingCharge();
                $doctorRevisitingChargeObj->doctor_id = $this->id;
                try{
                    $values_arr = json_decode($doctorRevisitingChargeConfig->configuration_value);

                    if(is_array($values_arr)){
                        
                        foreach($values_arr as $valObj){
                            if(isset($valObj->key) && $valObj->key=='revisiting_within'){
                                $doctorRevisitingChargeObj->revisiting_within=$valObj->value;
                            }
                            if(isset($valObj->key) && $valObj->key=='revisiting_fees'){
                                $doctorRevisitingChargeObj->revisiting_fees=$valObj->value;
                            }
                            if(isset($valObj->key) && $valObj->key=='revisiting_status'){
                                $doctorRevisitingChargeObj->revisiting_status=$valObj->value;
                            }
                        }
                    }else{
                        $doctorRevisitingChargeObj->revisiting_within=0;
                        $doctorRevisitingChargeObj->revisiting_fees=0;
                        $doctorRevisitingChargeObj->revisiting_status=0;
                    }
                    
                } catch (\Exception $ex) {
                    $doctorRevisitingChargeObj->revisiting_within=0;
                    $doctorRevisitingChargeObj->revisiting_fees=0;
                    $doctorRevisitingChargeObj->revisiting_status=0;
                }
                
                return $doctorRevisitingChargeObj;
            }else{
                return $this->doctorRevisitingCharge;
            }
        }
    }

    public function doctorDocuments()
    {
        return $this->hasMany(DoctorDocument::class);
    }

    public function getDoctorDocumentListAttribute(){
        $documentList = [];
        
        if(isset($this->doctorDocuments) && $this->doctorDocuments->count()>0){
            foreach($this->doctorDocuments as $doctorDocument){
                $documentList[$doctorDocument->id]=$doctorDocument->document;
            }
        }
        
        return $documentList;
    }

    public function getTitleAttribute($value)
    {
        
        return $value==null ? 'Dr' : config('constants.TITLE')[$value];
    }

    public function getDobAttribute($value)
    {
        if(!empty($value)){

            $dob = $value;
            $dob = \DateTime::createFromFormat("Y-m-d", $dob)->format("d/m/Y");

            return $dob;
        }
    }


    public function getStartedWorkingFromAttribute($value)
    {      if(!empty($value))
        {
            $joinDate = $value;
            $joinDate = \DateTime::createFromFormat("Y-m-d", $joinDate)->format("d/m/Y");

            return $joinDate;
        }
    }
     
    */
}