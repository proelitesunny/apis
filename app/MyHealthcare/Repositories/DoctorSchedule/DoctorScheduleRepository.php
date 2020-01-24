<?php

namespace App\MyHealthcare\Repositories\DoctorSchedule;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\MyHealthcare\Repositories\DoctorSchedule\DoctorScheduleInterface;
class DoctorScheduleRepository implements DoctorScheduleInterface
{
    public function getAvailableSlotList($request){

        if(!empty($request->has('start_date'))){

            $start_date=explode("/", $request->input('start_date'))[2]."-".explode("/", $request->input('start_date'))[1]."-".explode("/", $request->input('start_date'))[0];
        }
        else{

            $start_date=Carbon::now()->format('Y-m-d');
        }
        $records=DB::table('doctor_schedules')
                ->where('start_date','<=',$start_date)
                // ->where('schedule_type',1)
                ->where(['doctor_id'=>$request->input('doctor_id'),'hospital_id'=>$request->input('hospital_id')])
                ->get();
        
        if(count($records)<=0){
             abort(400, trans('errors.DOCTOR_SCEDULER_101'));
                }
        $day_of_week=Carbon::parse($start_date)->format('w');

        $records=DB::table('doctor_schedules')
        ->join('doctor_schedule_sessions','doctor_schedules.id','=','doctor_schedule_sessions.doctor_schedule_id')
        ->join('doctor_session_slots','doctor_schedule_sessions.id','=','doctor_session_slots.doctor_schedule_session_id')
        ->select('doctor_session_slots.id as ScheduleSlotId,start_time as StartTime',DB::raw('if(override_appointment_duration is null || override_appointment_duration="",end_time,ADDTIME(end_time,(select slot_duration from doctor_hospital where doctor_id=doctor_schedules.doctor_id and hospital_id=doctor_schedules.hospital_id order by id desc limit 0,1))) as EndTime,if(overridden_fees is null || overridden_fees="",(select fees from doctor_hospital where doctor_id=doctor_schedules.doctor_id and hospital_id=doctor_schedules.hospital_id order by id desc limit 0,1),override_fees) as Fees,if((select count(id) from bookings where doctor_session_slot_id=doctor_session_slots.id)>=1,0,1) as AvailabilityStatus'))
        ->where('start_date','<=',$start_date)
        ->where('visibility_a',1)
        ->where(['doctor_id'=>$request->doctor_id,'hospital_id'=>$request->hospital_id])
        ->where('day_of_week',$day_of_week)
        ->paginate(10)
        ->appends(["doctorCode"=>$request->doctor_code,'hospital_code'=>$request->hospital_code,'start_date'=>$request->start_date]);

        $response['doctors']=$records->toArray()['data'];
        $response['doctors']=$records->toArray()['data'];
        

       return $response;
    }
    public function getBookingList($request)
    {
      if($request->has('start_date')){

            $start_date=explode("/", $request->input('start_date'))[2]."-".explode("/", $request->input('start_date'))[1]."-".explode("/", $request->input('start_date'))[0];
        }
        else{

            $start_date=Carbon::now()->format('Y-m-d');
        }
        if($request->has('end_date')){

            $end_date=explode("/", $request->input('end_date'))[2]."-".explode("/", $request->input('end_date'))[1]."-".explode("/", $request->input('end_date'))[0];
        }
        else{

            $end_date=Carbon::parse($start_date)->addDays(7)->format('Y-m-d');
        }
       
        $records=Booking::join('patients','patients.id','=','bookings.patient_id')->where(['hospital_id'=>$request->input('hospital_id'),'doctor_id'=>$request->input('doctor_id')])
            ->whereBetween('booking_date',[$start_date,$end_date])
            ->select('bookings.id as booking_id','booking_date','booking_amount','booking_status','is_patient_arrived','booked_by','booking_code','first_name','last_name','mobile_no')
            ->paginate(10)
            ->appends(["doctor_code"=>$request->input('doctor_code'),'hospital_code'=>$request->input('hospital_code'),'start_date'=>Carbon::parse($start_date)->format('d/m/Y'),'end_date'=>Carbon::parse($end_date)->format('d/m/Y')])
            ;

      if(count($records)<=0){
             abort(400, trans('errors.BOOKINGS_105'));
                }

        $response['doctors']=$records->toArray()['data'];
        $response['pagination']=[
            'total' => $records->total(),
            'per_page' => $records->perPage(),
            'current_page' => $records->currentPage(),
            'last_page' => $records->lastPage(),
            'next_page_url' => $records->nextPageUrl(),
            'prev_page_url' => $records->previousPageUrl(),
            'from' => $records->firstItem(),
            'to' => $records->lastItem()];
      return $response;
    }             
}
