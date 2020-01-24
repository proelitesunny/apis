<?php
namespace App\MyHealthcare\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB as DB;
use App\User;
use App\Role;
use App\Permission;

class MigrationAclHelper extends Migration
{
    protected $permissions;
    protected $roles;
    protected $can_access;
    
    public function __construct() {
        
        $this->permissions = [];
        $this->roles = [];
        $this->can_access = [];
        
    }   
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissionObjs = [];
        
        foreach ($this->permissions as $key => $value) {

        	$permissionObj = Permission::create($value);
                $permissionObjs[] = $permissionObj;
        }
                       
        if(count($this->roles)>0 & count($permissionObjs)>0){
            foreach($this->roles as $role_id=>$role_name){
                foreach($permissionObjs as $permissionObj){                    
                    if(in_array($permissionObj->name,$this->can_access[$role_name])){
                        DB::table('permission_role')->insert(['permission_id'=>$permissionObj->id, 'role_id' =>$role_id]);
                    }
                }   
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->permissions as $key => $value) {
            
            $permisssion = Permission::where('name',$value['name'])->first();
            
            if($permisssion!=null){
                
                if(count($this->roles)>0){
                    foreach($this->roles as $role_id=>$role_name){
                        if(DB::table('permission_role')->where(['permission_id'=>$permisssion->id,'role_id'=>$role_id])->count()>0){
                            DB::table('permission_role')->where(['permission_id'=>$permisssion->id,'role_id'=>$role_id])->delete();
                        }
                    }
                }
                
                Permission::where('name',$value['name'])->forceDelete();                
            }
        }
    }
}


