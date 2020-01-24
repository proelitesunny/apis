<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Speciality extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    
    public function hospitalSpecialities()
    {
        return $this->belongsToMany(Hospital::class);
    }

    public function doctorSpecialities()
    {
        return $this->belongsToMany(Doctor::class);
    }

    /*
    public function specialityDetails()
    {
        return $this->hasMany(SpecialityDetail::class,'speciality_id');
    }
    
    */
    
}
