<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $dates = ['deleted_at'];
    protected $fillable = [];

    protected $table = 'test_results';
}
