<?php

namespace App\MyHealthcare\Repositories\Report;
use App\MyHealthcare\Helpers\ExcelReport;
use App\Models\Patient;
use App\Jobs\SendReportEmail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;
use Auth;

class PatientReportRepository implements PatientReportInterface
{
    use DispatchesJobs;
    
    private $patient;
    private $fileName; // file name - required   
    private $sheetName; // sheet name - required
    private $columnNames; // column names array - required   
    private $excelData;  // data array - required 
    private $excelFormat; // xls, xlsx and csv - required   
    private $fileOption; //export, download or store - required 
    private $extraParams;
        
    public function __construct(Patient $patient) {
        $this->patient = $patient;
    }
    
    public function exportReport($request) {
        
        $loggedInUser = Auth::getUser();
        $currentUser['email'] = $loggedInUser->email;
        $currentUser['name'] = $loggedInUser->admin->first_name.' '.$loggedInUser->admin->last_name;
        
        $todaysDate = Carbon::now()->format('Y-m-d');
        $fromDateObj = new Carbon(config('constants.excelReportSettings.duration_of_last'));
        $fromDate = $fromDateObj->format('Y-m-d');
        
        
        try{
            
            $this->fileName = 'patients_report';
            $this->sheetName = 'Patients Sheet 1';  
            $this->excelFormat = 'xls';
            $this->fileOption = 'store'; 
            $this->columnNames = []; // No need to specifiy if you apply transform method on model
            $this->excelData = $this->getData($request);      
            $this->extraParams = [];
            $this->extraParams['storagePath'] = public_path().config('constants.excelReportSettings.upload_path').'/patients';            
            
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
            $emailData['subject']='Fortis Health Care Patients Excel Report As On '.date('Y-m-d H:i A');
            $emailData['attach']=$filepath;
            $emailData['template']='emails.excel_report';
            $emailData['messageText']="Please find attached patients excel report document has between ".$fromDate." to ".$todaysDate.".";
            
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
        $patientReportData = [];
        $excelData = [];
        
        $todaysDate = Carbon::now()->format('Y-m-d');
        $fromDateObj = new Carbon(config('constants.excelReportSettings.duration_of_last'));
        $fromDate = $fromDateObj->format('Y-m-d');
        
        if(!empty($keyword)){
            $patientReportData = $this->patient->with('country','state','city','patientHealthDetails')
                                    ->where(function($query) use($fromDate,$todaysDate){
                                        $query->whereRaw(" (DATE(created_at) between '".($fromDate)."' and '".($todaysDate)."') || (DATE(updated_at) between '".($fromDate)."' and '".($todaysDate)."')");
                                    })
                                    ->where(function($query) use($keyword){
                                        $query->where('patient_code', 'LIKE', '%'. $keyword .'%')
                                              ->orWhere('first_name', 'LIKE', '%'. $keyword .'%')
                                              ->orWhere('last_name', 'LIKE', '%'. $keyword .'%')
                                              ->orWhereRaw("concat(first_name,' ',last_name) like '%".$keyword."%' ")
                                              ->orWhere('id_number', 'LIKE', '%'. $keyword . '%')
                                              ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                                              ->orWhere('mobile_no', 'LIKE', '%'. $keyword .'%');
                                    })
                                    ->orderBy('first_name', 'ASC')->get();                               
                    
        }else{
            $patientReportData = $this->patient->with('country','state','city','patientHealthDetails')
                    ->whereRaw(" (DATE(created_at) between '".($fromDate)."' and '".($todaysDate)."') || (DATE(updated_at) between '".($fromDate)."' and '".($todaysDate)."')")
                    ->orderBy('first_name', 'ASC')->get();
        }                  
        
        if($patientReportData!=null and count($patientReportData)>0){
            
            $excelData = $patientReportData->transform(function ($patient, $key) { 
                return [
                    'MRN' => $patient->patient_code,
                    'Name' => $patient->full_name,
                    'Email' => $patient->email,
                    'Mobile No.' => $patient->mobile_no,
                    'Date Of Birth' => $patient->dob,
                    'Gender' => $patient->gender,
                    'Blood Group' => $patient->blood_group,
                    'Guardian Name' => $patient->guardian_name,
                    'Status' => $patient->is_active,
                    'Is Verified' => $patient->is_verified,
                    'Created At' => !empty($patient->created_at) ? $patient->created_at->format('Y-m-d H:i:s') : null,
                    'Updated At' => !empty($patient->updated_at) ? $patient->updated_at->format('Y-m-d H:i:s') : null,
                ];                
            })->toArray();
            
        }        
        
        return $excelData;
    }

    public function importReport($request) {
        
    }

}

