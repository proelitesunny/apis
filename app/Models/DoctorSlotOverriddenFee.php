<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSlotOverriddenFee extends Model
{
    protected static $unguarded = true;
    protected $table = 'doctor_slot_overridden_fees';
    
    public function doctorSessionSlot()
    {
        return $this->belongsTo(DoctorSessionSlot::class);
    }
}
