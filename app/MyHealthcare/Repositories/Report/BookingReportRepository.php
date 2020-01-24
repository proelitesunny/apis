<?php

namespace App\MyHealthcare\Repositories\Report;
use App\MyHealthcare\Helpers\ExcelReport;
use App\Models\Booking;
use App\Jobs\SendReportEmail;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;
use Auth;

class BookingReportRepository implements BookingReportInterface
{
    use DispatchesJobs;
    
    private $booking;
    private $fileName; // file name - required   
    private $sheetName; // sheet name - required
    private $columnNames; // column names array - required   
    private $excelData;  // data array - required 
    private $excelFormat; // xls, xlsx and csv - required   
    private $fileOption; //export, download or store - required 
    private $extraParams;
    
    public function __construct(Booking $booking) {
        $this->booking = $booking;
    }

    public function exportReport($request) {

        $loggedInUser = Auth::getUser();
        $currentUser['email'] = $loggedInUser->email;
        $currentUser['name'] = $loggedInUser->admin->first_name.' '.$loggedInUser->admin->last_name;
        
        $todaysDate = Carbon::now()->format('Y-m-d');
        $fromDateObj = new Carbon(config('constants.excelReportSettings.duration_of_last'));
        $fromDate = $fromDateObj->format('Y-m-d');
        
        try{
            $this->fileName = 'bookings_report';
            $this->sheetName = 'Bookings Sheet 1';  
            $this->excelFormat = 'xls';
            $this->fileOption = 'store'; 
            $this->columnNames = []; // No need to specifiy if you apply transform method on model
            $this->excelData = $this->getData($request);      
            $this->extraParams = [];
            $this->extraParams['storagePath'] = public_path().config('constants.excelReportSettings.upload_path').'/bookings';            
            
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
            $emailData['subject']='Fortis Health Care Bookings Excel Report As On '.date('Y-m-d H:i A');
            $emailData['attach']=$filepath;
            $emailData['template']='emails.excel_report';
            $emailData['messageText']="Please find attached bookings excel report document has between ".$fromDate." to ".$todaysDate.".";
            
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
        $bookingReportData = [];
        $excelData = [];
        
        $todaysDate = Carbon::now()->format('Y-m-d');
        $fromDateObj = new Carbon(config('constants.excelReportSettings.duration_of_last'));
        $fromDate = $fromDateObj->format('Y-m-d');
        
        $key = null;
        if (array_search(strtolower($keyword), array_map('strtolower', config('constants.APPOINTMENT_BOOKING'))) !== false) {
            $key = array_search(strtolower($keyword), array_map('strtolower', config('constants.APPOINTMENT_BOOKING')));
        }
        
        
        if(!empty($keyword)){            
            
            $bookingReportData = $this->booking->with('patient','doctor','doctor.user','hospital','transaction')
                                ->where(function($query) use($fromDate,$todaysDate){
                                    $query->whereRaw(" (DATE(created_at) between '".($fromDate)."' and '".($todaysDate)."') || (DATE(updated_at) between '".($fromDate)."' and '".($todaysDate)."')");
                                })
                                ->where(function($query) use($keyword,$key){
                                    
                                    $query->where('booking_code', 'LIKE', '%' . $keyword . '%');
                                    
                                    $query->orWhere('booking_status', $key);
                                    
                                    $query->orWhereHas('doctor', function ($query) use ($keyword) {
                                        $query->where('doctor_code', 'LIKE', '%'.$keyword.'%')
                                                ->orWhere('first_name', 'LIKE', '%'.$keyword.'%')
                                                ->orWhere('last_name', 'LIKE', '%'.$keyword.'%')
                                                ->orWhereRaw("concat(first_name,' ',last_name) like '%".$keyword."%' ");
                                    });

                                    $query->orWhereHas('doctor.user', function ($query) use ($keyword) {
                                        $query->where('email', 'LIKE', '%'.$keyword.'%');
                                    });
                                    
                                    $query->orWhereHas('patient', function ($query) use ($keyword) {
                                        $query->where('patient_code', 'LIKE', '%'. $keyword .'%')
                                              ->orWhere('first_name', 'LIKE', '%'. $keyword .'%')
                                              ->orWhere('last_name', 'LIKE', '%'. $keyword .'%')
                                              ->orWhereRaw("concat(first_name,' ',last_name) like '%".$keyword."%' ")
                                              ->orWhere('id_number', 'LIKE', '%'. $keyword . '%')
                                              ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                                              ->orWhere('mobile_no', 'LIKE', '%'. $keyword .'%');
                                    });
                        
                                })
                                ->orderBy('id', 'DESC')->get();
        }else{
            $bookingReportData = $this->booking->with('patient','doctor','doctor.user','hospital','transaction')
                                ->whereRaw(" (DATE(created_at) between '".($fromDate)."' and '".($todaysDate)."') || (DATE(updated_at) between '".($fromDate)."' and '".($todaysDate)."')")
                                ->orderBy('id', 'DESC')->get();
        }        
        
        //dd($bookingReportData->toArray());
        
        if($bookingReportData!=null and count($bookingReportData)>0){
            
            $excelData = $bookingReportData->transform(function ($booking, $key) { 
                return [
                    'Booking ID' => $booking->booking_code,
                    'Appointment On' => $booking->booking_date.' '.$booking->booking_time,
                    'Booking Status' => $booking->status_booking,
                    'Consultation Charges' => $booking->amount,
                    'Patient MRN' => $booking->patient->patient_code,
                    'Patient Name' => $booking->patient->full_name,
                    'Doctor Code' => $booking->doctor->doctor_code,
                    'Doctor Name' => $booking->doctor->first_name.' '.$booking->doctor->last_name,
                    'Created At' => !empty($booking->created_at) ? $booking->created_at->format('Y-m-d H:i:s') : null,
                    'Updated At' => !empty($booking->updated_at) ? $booking->updated_at->format('Y-m-d H:i:s') : null,
                ];
            })->toArray();
            
        }   
        
        return $excelData;
    }

    public function importReport($request) {
        
    }

}