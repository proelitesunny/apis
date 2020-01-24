<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPatientIndex extends Model
{
    use SoftDeletes;

    protected $dates = ['updated_at'];


    public function patient()
    {
        return $this->hasMany(Patient::class);
    }

    public function patientHisMapping()
    {
        return $this->hasMany(PatientHisMapping::class);
    }
}
