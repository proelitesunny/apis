<?php

namespace App\MyHealthcare\Repositories\SmsLog;

use App\Models\SmsLog;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SmsLogRepository implements SmsLogInterface{
    
    private $smsLog; 
    
    public function __construct(SmsLog $smsLog) {
        
        $this->smsLog = $smsLog;
                
    }


    public function find($id){
        try{
            $smsLog = $this->smsLog->findOrFail($id);
            return $smsLog;
        } catch (ModelNotFoundException $ex) {
            throw new ModelNotFoundException($ex->getMessage(),$ex->getCode());
        }
    }

    public function getCount() {
        try{
            return $this->smsLog->count();
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }

    public function paginateSmsLogs($keyword = null){
        try{
            $smsLogs = $this->smsLog->where(function($query) use($keyword) {
                                            if ($keyword) {
                                                
                                                $query->where('mobile_no', $keyword) 
                                                      ->orWhere('message', 'LIKE', '%'.$keyword.'%')                                           
                                                      ->orWhere('sent_at', 'LIKE', '%'.$keyword.'%');                                            
                                            }
                                        })
                                        ->orderBy('sent_at', 'DESC')
                                        ->paginate(10);
            return $smsLogs;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }

}