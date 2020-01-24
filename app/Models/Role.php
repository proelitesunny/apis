<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Zizaco\Entrust\EntrustRole;

class Role extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'id', 'name', 'display_name', 'description'
    ];
    
    /*
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }
    */
}
