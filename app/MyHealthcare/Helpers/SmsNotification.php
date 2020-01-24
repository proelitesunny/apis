<?php

namespace App\MyHealthcare\Helpers;

use Validator;
use Carbon\Carbon;
use App\Models\SmsLog;

class SmsNotification {
    
    private static $mobile_no;
    private static $message;

    public static function setProvider(){
        //Set SMS Service provider details
    }
    
    public static function validateContents(){
        
        $inputs['mobile_no'] = self::$mobile_no;
        $inputs['message'] = self::$message;
        
        $validator = Validator::make($inputs,[
                        'mobile_no' => 'required|numeric|regex:/[0-9]{10}/',
                        'message' => 'bail|required|min:3|max:160',
                    ]);
        
        if ($validator->fails()) {            
            $error_messages=self::filterValidatorMessages($validator->errors()->messages());
            logger()->error($error_messages);
            return false;
        }else{
            return true;
        }
        
    }


    public static function send($mobile_no, $message){                
                
        self::$mobile_no = $mobile_no;
        self::$message = $message;
        
        if(self::validateContents()){
            
            self::setProvider();
                        
            $response['status'] = 200;
            
            if($response['status'] == 200){
                logger()->info(['status'=>$response['status'],'mobile_no'=>self::$mobile_no,'message'=>self::$message]); 
                
                try{
                    
                    $smsLogger = ['mobile_no'=>self::$mobile_no,'message'=>self::$message,'sent_at'=>Carbon::now()->format('Y-m-d H:i:s')];
                    
                    SmsLog::create($smsLogger);
                    
                } catch (\Exception $e){
                    logger()->error($e->getMessage());
                }
            }else{
                logger()->error(['status'=>$response['status'],'mobile_no'=>self::$mobile_no,'message'=>self::$message]); 
            }
        }
        
    }
    
    public static function filterValidatorMessages($messages){
        
        $response['errors'] = [];            

        foreach($messages as $err_col => $err_arr ){                
                
            if(is_array($err_arr)){
                
                $response['errors'][$err_col] = isset($err_arr[0]) ? str_replace('field ','',$err_arr[0]) : '';
            }else{
                $response['errors'][$err_col] = $err_arr;
            }
        }

        return $response;
    }
    
}
