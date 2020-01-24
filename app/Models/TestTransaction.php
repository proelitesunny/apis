<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestTransaction extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function booking()
    {
        return $this->belongsTo(TestBookings::class, "test_booking_id");
    }

    public function getPaymentStatusAttribute()
    {
        if($this->getAttributes()['payment_type']==2 && $this->getAttributes()['payment_status']==0){
            return "Pay On Arrival";
        }elseif($this->getAttributes()['payment_type']!=2 && $this->getAttributes()['payment_status']==1){
            return "Success";
        }else{
            return "Pending";
        }
    }

}
