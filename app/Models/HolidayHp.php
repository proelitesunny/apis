<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HolidayHp extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'hospital_id','hospital_holiday_id'
      ];

    protected $table = 'holiday_hp';

    protected $dates = ['deleted_at'];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function hospitalHoliday(){
    	return $this->belongsTo(HospitalHoliday::class);
    }

}
