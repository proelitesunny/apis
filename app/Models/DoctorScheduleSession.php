<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorScheduleSession extends Model
{
    public function slots()
    {
        return $this->hasMany(DoctorSessionSlot::class);
    }

    public function doctorSchedule()
    {
        return $this->belongsTo(DoctorSchedule::class);
    }
}
