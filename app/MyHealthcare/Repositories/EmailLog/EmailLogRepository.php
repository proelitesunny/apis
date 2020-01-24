<?php

namespace App\MyHealthcare\Repositories\EmailLog;

use App\Models\EmailLog;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmailLogRepository implements EmailLogInterface{
    
    private $emailLog; 
    
    public function __construct(EmailLog $emailLog) {
        
        $this->emailLog = $emailLog;
                
    }


    public function find($id){
        try{
            $emailLog = $this->emailLog->findOrFail($id);
            return $emailLog;
        } catch (ModelNotFoundException $ex) {
            throw new ModelNotFoundException($ex->getMessage(),$ex->getCode());
        }
    }

    public function getCount() {
        try{
            return $this->emailLog->count();
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }

    public function paginateEmailLogs($keyword = null){
        try{
            $emailLogs = $this->emailLog->where(function($query) use($keyword) {
                                            if ($keyword) {
                                                if(in_array(strtolower($keyword),array_map('strtolower',array_values(config('constants.email_params.email_type'))))){
                                                    $keyIndex = array_search(strtolower($keyword),array_map('strtolower',array_values(config('constants.email_params.email_type'))));
                                                    $keyList = array_keys(config('constants.email_params.email_type'));
                                                    $dbSearchValue = $keyList[$keyIndex];
                                                    $query->where('email_type','LIKE', '%'.$dbSearchValue.'%');
                                                }else if(in_array(strtolower($keyword),array_map('strtolower',array_values(config('constants.email_params.content_type'))))){
                                                    $keyIndex = array_search(strtolower($keyword),array_map('strtolower',array_values(config('constants.email_params.content_type'))));
                                                    $keyList = array_keys(config('constants.email_params.content_type'));
                                                    $dbSearchValue = $keyList[$keyIndex];
                                                    $query->where('content_type','LIKE', '%'.$dbSearchValue.'%');
                                                }else{
                                                    $query->where('from_address', 'LIKE', '%'.$keyword.'%')
                                                          ->orWhere('to_address', 'LIKE', '%'.$keyword.'%')                                           
                                                          ->orWhere('subject', 'LIKE', '%'.$keyword.'%')                                           
                                                          ->orWhere('body', 'LIKE', '%'.$keyword.'%');                                            
                                                }
                                            }
                                        })
                                        ->orderBy('sent_at', 'DESC')
                                        ->paginate(10);
            return $emailLogs;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(),$ex->getCode());
        }
    }

}