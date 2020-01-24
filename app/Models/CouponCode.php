<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponCode extends Model
{
    protected $dates = ['deleted_at'];
    protected $fillable = [];

    protected $table = 'coupon_codes';
}