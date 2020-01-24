<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function testHospitalMappings()
    {
        return $this->hasMany(TestHospitalMapping::class);
    }

    public function testBooking()
    {
        return $this->hasMany(TestBookings::class);
    }
}
