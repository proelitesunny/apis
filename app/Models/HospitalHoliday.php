<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HospitalHoliday extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'date','reason','created_by','updated_by'
    ];
    
    protected $dates = ['deleted_at'];

    protected $table = 'hospital_holidays';

    public function hospitals(){
    	return $this->belongsToMany(Hospital::class,'holiday_hp');
    }

    public function getDateAttribute($value)
    {
        if(!empty($value)){

            $date = $value;
            $date = \DateTime::createFromFormat("Y-m-d", $date)->format("d/m/Y");

            return $date;
        }
    }
}