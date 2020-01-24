<?php

namespace App\MyHealthcare\Repositories\EmailLog;

interface EmailLogInterface{
    
    public function find($id);
    
    public function getCount();
    
    public function paginateEmailLogs($keyword=null);
        
}