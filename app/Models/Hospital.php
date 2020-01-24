<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hospital extends Model
{
    use SoftDeletes;

    protected $date = ['deleted_at'];
    
    
    public function hospitalSpecialities()
    {
        return $this->belongsToMany('App\Models\Speciality', 'hospital_speciality');
    }

    public function doctorHospital()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_hospital');
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class,'hospital_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /*
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
    }*/
}
