<?php

namespace App\MyHealthcare\Repositories\Report;
use App\MyHealthcare\Helpers\ExcelReport;
use App\Models\Doctor;
use App\Jobs\SendReportEmail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;
use Auth;

class DoctorReportRepository implements DoctorReportInterface
{
    use DispatchesJobs;
    
    private $doctor;
    private $fileName; // file name - required   
    private $sheetName; // sheet name - required
    private $columnNames; // column names array - required   
    private $excelData;  // data array - required 
    private $excelFormat; // xls, xlsx and csv - required   
    private $fileOption; //export, download or store - required 
    private $extraParams;
    
    public function __construct(Doctor $doctor) {
        $this->doctor = $doctor;
    }

    public function getData($request){
        
        $keyword = trim($request->input('keyword'));
        $doctorReportData = [];
        $excelData = [];
        
        $todaysDate = Carbon::now()->format('Y-m-d');
        $fromDateObj = new Carbon(config('constants.excelReportSettings.duration_of_last'));
        $fromDate = $fromDateObj->format('Y-m-d');
        
        if(!empty($keyword)){
            
            $doctorReportData = $this->doctor->with('user', 'doctorHospital', 'doctorSpecialities')
                                ->where(function($query) use($fromDate,$todaysDate){
                                    $query->whereRaw(" (DATE(created_at) between '".($fromDate)."' and '".($todaysDate)."') || (DATE(updated_at) between '".($fromDate)."' and '".($todaysDate)."')");
                                })
                                ->where(function($query) use($keyword){
                                    $query->where('doctor_code', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('first_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('last_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhereRaw("concat(first_name,' ',last_name) like '%".$keyword."%' ")
                                    ->orWhere('address', 'LIKE', '%'.$keyword.'%')
                                    ->orWhereHas('user', function ($query) use ($keyword) {
                                        $query->where('email', 'LIKE', '%'.$keyword.'%');
                                        $query->orWhere('mobile_no', 'LIKE', '%'.$keyword.'%');
                                    })
                                    ->orWhereHas('doctorHospital', function ($query) use ($keyword) {
                                        $query->where('name', 'LIKE', '%'.$keyword.'%');
                                    })
                                    ->orWhereHas('doctorSpecialities', function ($query) use ($keyword) {
                                        $query->where('name', 'LIKE', '%'.$keyword.'%');
                                    });
                                })
                                ->orderBy('first_name', 'ASC')->get();                               
            
        }else{
            $doctorReportData = $this->doctor->with('user', 'doctorHospital', 'doctorSpecialities')
                                ->whereRaw(" (DATE(created_at) between '".($fromDate)."' and '".($todaysDate)."') || (DATE(updated_at) between '".($fromDate)."' and '".($todaysDate)."')")
                                ->orderBy('first_name', 'ASC')->get();
        }
        
        //dd($doctorReportData->toArray());
        
        if($doctorReportData!=null and count($doctorReportData)>0){
            $excelData = $doctorReportData->transform(function ($doctor, $key) { 
                return [
                    //'Id'=> $doctor->id,
                    'Doctor Code'=> $doctor->doctor_code,
                    'Doctor Name'=> $doctor->first_name.' '.$doctor->last_name,
                    'Gender'=> $doctor->gender,
                    'Email'=> $doctor->user->email,
                    'Mobile No.'=> $doctor->user->mobile_no,
                    //'Office No.' => $doctor->office_number,
                    'Specialities' => ((!empty($doctor->doctorSpecialities) && $doctor->doctorSpecialities->count() > 0) ? implode(',',$doctor->doctorSpecialities->pluck('name')->toArray()) : null),
                    'Designation' => $doctor->designation,
                    'Experience' => $doctor->experience,
                    'Fees' => $doctor->fees,
                    'Revisiting Charges Status' => ((!empty($doctor->doctorRevisitingCharge) && $doctor->doctorRevisitingCharge->revisiting_status ==1 ) ? 'Enabled': 'Disabled'),
                    'Revisiting Within Days' => ((!empty($doctor->doctorRevisitingCharge) && isset($doctor->doctorRevisitingCharge->revisiting_within) ) ? $doctor->doctorRevisitingCharge->revisiting_within: null),
                    'Revisiting Fees' => ((!empty($doctor->doctorRevisitingCharge) && isset($doctor->doctorRevisitingCharge->revisiting_fees) ) ? $doctor->doctorRevisitingCharge->revisiting_fees : null),
                    'Status' => $doctor->user->is_active,
                    'Is Verified' => $doctor->user->is_verified,
                    'Created At' => !empty($doctor->created_at) ? $doctor->created_at->format('Y-m-d H:i:s') : null,
                    'Updated At' => !empty($doctor->updated_at) ? $doctor->updated_at->format('Y-m-d H:i:s') : null,
                    //'Deleted At' => !empty($doctor->deleted_at) ? $doctor->deleted_at->format('Y-m-d H:i:s') : null,
                ];
            })->toArray();
        }               
        
        //$this->columnNames = ['id'=>'Id','doctor_code'=>'Doctor Code','name'=>'Doctor Name','gender'=>'Gender','email'=>'Email','mobile_no'=>'Mobile No.','office_number' =>'Office No.','designation' => 'Designation','experience' => 'Experience','fees' =>'Fees','is_active' =>'Status','is_verified' =>'Is Verified','created_at' =>'Created At','updated_at' =>'Updated At','deleted_at' =>'Deleted At'];        

        return $excelData;
    }

    public function importReport($request) {
        //write code for import execel report
    }
    
    public function exportReport($request) { 
                
        $loggedInUser = Auth::getUser();
        $currentUser['email'] = $loggedInUser->email;
        $currentUser['name'] = $loggedInUser->admin->first_name.' '.$loggedInUser->admin->last_name;
        
        $todaysDate = Carbon::now()->format('Y-m-d');
        $fromDateObj = new Carbon(config('constants.excelReportSettings.duration_of_last'));
        $fromDate = $fromDateObj->format('Y-m-d');
        
        try{
            $this->fileName = 'doctors_report';
            $this->sheetName = 'Doctors Sheet 1';  
            $this->excelFormat = 'xls';
            $this->fileOption = 'store'; 
            $this->columnNames = []; // No need to specifiy if you apply transform method on model
            $this->excelData = $this->getData($request);      
            $this->extraParams = [];
            $this->extraParams['storagePath'] = public_path().config('constants.excelReportSettings.upload_path').'/doctors';            
            
            if(empty($this->excelData) || count($this->excelData)==0){
                throw new \Exception("Excel report could not be exported if records are empty.",1001);
            }
            
            $excelReportResponse = ExcelReport::export($this->fileName,$this->sheetName,$this->columnNames,$this->excelData,$this->excelFormat,$this->fileOption,$this->extraParams);
                        
            $filepath = join(DIRECTORY_SEPARATOR, array($excelReportResponse->storagePath, $excelReportResponse->filename.'.'.$excelReportResponse->ext));
            
            $emailData = [];
            $emailData['to']=['address'=>$currentUser['email'],'name'=>$currentUser['name']];
            $emailData['cc']=['address'=>'aankit@ndtv.com','name'=>'Ankit'];
            $emailData['bcc']=['address'=>'aankit@ndtv.com','name'=>'Ankit'];
            $emailData['replyTo']=['address'=>'aankit@ndtv.com','name'=>'Ankit'];
            $emailData['subject']='Fortis Health Care Doctors Excel Report As On '.date('Y-m-d H:i A');
            $emailData['attach']=$filepath;
            $emailData['template']='emails.excel_report';
            $emailData['messageText']="Please find attached doctors excel report document has between ".$fromDate." to ".$todaysDate.".";
            
            $this->dispatch(new SendReportEmail($emailData));
            
        }catch(\Exception $e){
            if($e->getCode()==1001){
                throw new \Exception($e->getMessage(),1001);
            }else{
                throw new \Exception('Export excel report failed.');
            }
        }    
    }
}

