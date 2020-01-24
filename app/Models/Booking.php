<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    
    public function patient()
    {
        return $this->belongsTo(Patient::class,'patient_id','id');
    }
    
    public function hospital()
    {
        return $this->belongsTo(Hospital::class,'hospital_id','id');
    }
    
    public function transaction()
    {
        return $this->transactions()->latest()->first();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class,'booking_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class,'doctor_id','id');
    }


}
