<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSessionSlot extends Model
{
    public function doctorScheduleSession()
    {
        return $this->belongsTo(DoctorScheduleSession::class);
    }
}
