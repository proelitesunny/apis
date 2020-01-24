<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    public function sessions()
    {
        return $this->hasMany(DoctorScheduleSession::class);
    }
    
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
    /*
    public function getScheduleTypeTextAttribute($value){
        return ($this->schedule_type==0) ? 'Weekly' : 'Fixed';
    }

    public function getStatusDisplayAttribute()
    {
        return config('constants.DOCTOR_SCHEDULE_STATUS')[$this->status];
    }*/
}
