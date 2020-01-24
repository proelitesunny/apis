<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorHospital extends Model
{
    use SoftDeletes;

    protected $fillable = ['doctor_id','hospital_id', 'department_id','designation','slot_duration', 'fees', 'revisiting_within', 'revisiting_fees', 'revisiting_status', 'fortis_registration_no'];

    protected $table = 'doctor_hospital';

    protected $dates = ['deleted_at'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
