<?php

namespace App\MyHealthcare\Repositories\SmsLog;

interface SmsLogInterface{
    
    public function find($id);
    
    public function getCount();
    
    public function paginateSmsLogs($keyword=null);
        
}