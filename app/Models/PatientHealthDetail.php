<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientHealthDetail extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    /*
    public function getAllergiesTypeAttribute($value)
    {        
        return config('constants.ALLERGIES_TYPE')[$value];
    }
     
    */
}
