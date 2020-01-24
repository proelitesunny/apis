<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestRefund extends Model {

    //use SoftDeletes;

    //protected $dates = ['deleted_at'];
    
    public function refund(){
        $this->belongsTo(TestRefund::class,"test_booking_id");
    }
    
    public function testbookings()
    {
        return $this->belongsTo(TestBookings::class,"test_booking_id");
    }
    

}
