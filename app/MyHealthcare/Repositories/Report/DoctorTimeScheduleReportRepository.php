<?php

namespace App\MyHealthcare\Repositories\Report;
use App\MyHealthcare\Helpers\ExcelReport;
use App\Models\DoctorTimeSchedule;
use App\Jobs\SendReportEmail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;
use Auth;
use Entrust;

class DoctorTimeScheduleReportRepository implements DoctorTimeScheduleReportInterface
{
    
    use DispatchesJobs;
    
    private $doctorTimeSchedule;
    private $fileName; // file name - required   
    private $sheetName; // sheet name - required
    private $columnNames; // column names array - required   
    private $excelData;  // data array - required 
    private $excelFormat; // xls, xlsx and csv - required   
    private $fileOption; //export, download or store - required 
    private $extraParams;
    
    public function __construct(DoctorTimeSchedule $doctorTimeSchedule) {
        $this->doctorTimeSchedule = $doctorTimeSchedule;
    }
    
    public function exportReport($request) {
        
        $loggedInUser = Auth::getUser();
        $currentUser = [];
        
        $todaysDate = Carbon::now()->format('Y-m-d');
        $fromDateObj = new Carbon(config('constants.excelReportSettings.duration_of_last'));
        $fromDate = $fromDateObj->format('Y-m-d');
        
        
        if(Entrust::hasRole('doctor')){
            
        }else{
            $currentUser['email'] = $loggedInUser->email;
            $currentUser['name'] = $loggedInUser->admin->first_name.' '.$loggedInUser->admin->last_name;
        }
        
        
        try{
            $this->fileName = 'doctor_time_schedules_report';
            $this->sheetName = 'Doctor Time Schedules Sheet 1';  
            $this->excelFormat = 'xls';
            $this->fileOption = 'store'; 
            $this->columnNames = []; // No need to specifiy if you apply transform method on model
            $this->excelData = $this->getData($request);      
            $this->extraParams = [];
            $this->extraParams['storagePath'] = public_path().config('constants.excelReportSettings.upload_path').'/doctor_time_schedules';            
            
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
            $emailData['subject']='Fortis Health Care Doctor Time Schedules Excel Report As On '.date('Y-m-d H:i A');
            $emailData['attach']=$filepath;
            $emailData['template']='emails.excel_report';
            $emailData['messageText']="Please find attached doctor time schedules excel report document has between ".$fromDate." to ".$todaysDate.".";
            
            $this->dispatch(new SendReportEmail($emailData));
            
        }catch(\Exception $e){
            if($e->getCode()==1001){
                throw new \Exception($e->getMessage(),1001);
            }else{
                throw new \Exception('Export excel report failed.');
            }
        }            
        
    }

    public function getData($request) {
        
        $keyword = trim($request->input('keyword'));
        $doctorTimeScheduleReportData = [];
        $excelData = [];

        $todaysDate = Carbon::now()->format('Y-m-d');
        $fromDateObj = new Carbon(config('constants.excelReportSettings.duration_of_last'));
        $fromDate = $fromDateObj->format('Y-m-d');

        if(!empty($keyword)){
            $doctorTimeScheduleReportData = $this->doctorTimeSchedule->with('timeSlots','doctor','doctor.user','doctor.doctorSpecialities','doctor.doctorHospital')
                    ->where(function($query) use($fromDate,$todaysDate){
                        $query->whereRaw(" (DATE(created_at) between '".($fromDate)."' and '".($todaysDate)."') || (DATE(updated_at) between '".($fromDate)."' and '".($todaysDate)."')");
                    })
                    ->where(function($query) use($keyword){
                        
                        $query->whereHas('doctor', function ($query) use ($keyword) {
                            $query->where('doctor_code', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('first_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhere('last_name', 'LIKE', '%'.$keyword.'%')
                                    ->orWhereRaw("concat(first_name,' ',last_name) like '%".$keyword."%' ");
                        });
                        
                        $query->orWhereHas('doctor.user', function ($query) use ($keyword) {
                            $query->where('email', 'LIKE', '%'.$keyword.'%');
                        });
                        
                        $query->orWhereHas('doctor.doctorSpecialities', function ($query) use ($keyword) {
                            $query->where('name', 'LIKE', '%'.$keyword.'%');
                        });
                    })
                    ->orderBy('id','DESC')->get();
        }else{
            $doctorTimeScheduleReportData = $this->doctorTimeSchedule->with('timeSlots','doctor','doctor.user','doctor.doctorSpecialities','doctor.doctorHospital')
                    ->whereRaw(" (DATE(created_at) between '".($fromDate)."' and '".($todaysDate)."') || (DATE(updated_at) between '".($fromDate)."' and '".($todaysDate)."')")
                    ->orderBy('id','DESC')->get();
        }
        
        
        if($doctorTimeScheduleReportData!=null and count($doctorTimeScheduleReportData)>0){

            $excelData = $doctorTimeScheduleReportData->transform(function ($doctorimeSchedule, $key) { 
                
                return [
                        'Doctor Code' => $doctorimeSchedule->doctor->doctor_code,
                        'Doctor Name' => $doctorimeSchedule->doctor->first_name.' '.$doctorimeSchedule->doctor->last_name,
                        'Email'=> $doctorimeSchedule->doctor->user->email,
                        'Specialities' => ((!empty($doctorimeSchedule->doctor->doctorSpecialities) && $doctorimeSchedule->doctor->doctorSpecialities->count() > 0) ? implode(',',$doctorimeSchedule->doctor->doctorSpecialities->pluck('name')->toArray()) : null),
                        'Fees' => ((!empty($doctorimeSchedule->timeSlots) && $doctorimeSchedule->timeSlots->count() > 0) ? ($doctorimeSchedule->timeSlots->first()->fees) : 0),
                        'Available On' => ((!empty($doctorimeSchedule->timeSlots) && $doctorimeSchedule->timeSlots->count() > 0) ? ($doctorimeSchedule->schedule_days_of_week.': '.$doctorimeSchedule->timeSlots->first()->start_time.' - '.$doctorimeSchedule->timeSlots->first()->end_time) : null),
                        'Status' => (($doctorimeSchedule->status == 1 || $doctorimeSchedule->status==true) ? 'Active' : 'Inactive'),
                        'Created At' => !empty($doctorimeSchedule->created_at) ? $doctorimeSchedule->created_at->format('Y-m-d H:i:s') : null,
                        'Updated At' => !empty($doctorimeSchedule->updated_at) ? $doctorimeSchedule->updated_at->format('Y-m-d H:i:s') : null,
                    ];
            })->toArray();
        }                
        
        return $excelData;
    }

    public function importReport($request) {
        
    }

}