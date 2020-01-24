<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function booking()
    {
        return $this->belongsTo(Booking::class,'booking_id','id');
    }
    
    /**
     * Get the refund record associated with the transactions.
     */
    public function refund()
    {
        return $this->hasOne('App\Models\Refund','transaction_id','id');
    }

    /*public function getPaymentModeAttribute()
    {        
        return config('constants.PAYMENT_MODE')[$this->payment_type];
    }*/

    public function setPaymentTypeAttribute($value)
    {
        $this->attributes['payment_type'] = !is_null($value)?config('constants.PAYMENT_MODE_INTERNAL')[strtolower($value)]:$this->attributes['payment_type'];
    }

    /*public function getPaymentStatusAttribute()
    {
        return config('constants.PAYMENT_STATUS')[$this->attributes['payment_status']];
    }*/
}
