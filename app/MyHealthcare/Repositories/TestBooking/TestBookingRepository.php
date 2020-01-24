<?php

namespace App\MyHealthcare\Repositories\TestBooking;

use App\MyHealthcare\Repositories\TestBooking\TestBookingInterface;
use App\Models\TestBooking;
use App\Models\Test;
use Illuminate\Support\Facades\DB;
use App\Models\TestResult;
use App\Models\TestTransaction;

class TestBookingRepository implements TestBookingInterface {

    private $testResult;

    public function __construct(TestResult $testResult) {
        $this->testResult = $testResult;
    }

    public function getTestAppointments($params) {
        try {
            $timeZone = config('source.timezone.user');
            $hospitalId = 0;
            $addDays = config('source.appointment_add_days');
            $start_date = \Carbon\Carbon::now($timeZone)->format('Y-m-d');
            $end_date = \Carbon\Carbon::now($timeZone)->addDay($addDays)->format('Y-m-d');
            $pagination = config('source.new_patient_pagination');
            $passedDataAction = array();
            $modified_date = "";

            $pendingStatus = config('source.booking_status.pending');
            $bookingStatus = [
                config('source.booking_status.confirmed'),
                config('source.booking_status.canceled'),
                config('source.booking_status.rescheduled')
            ];

            if (!empty($params['appointment_date'])) {
                $start_date = \Carbon\Carbon::parse($params['appointment_date'])->format('Y-m-d');
                $end_date = $start_date;
                $passedDataAction['isStartDate'] = $start_date;
            }
            $details = TestBooking::whereHas('patient', function($query) {
                        $query->where('is_active',1);
                    })

                    ->whereHas('hospital', function($queryHospital) use($hospitalId) {
                        if ($hospitalId > 0) {
                            $queryHospital->where('id', $hospitalId);
                         }
                    })
                    ->whereHas('transactions', function($query) {
                    })->with(array('patient.patientHasManyHisMapping' => function($query) {
                    }))
                    ->when(isset($params['test_booking_code']), function($query) use($params){
                        return $query->where('test_booking_code', $params['test_booking_code']);
                    })
                    ->when(isset($params['from_datetime']), function($query) use($params){
                        return $query->when(isset($params['to_datetime']), function($query) use($params){
                            return $query->whereBetween('updated_at',[$params['from_datetime'], $params['to_datetime']]);
                        });
                    })
                    ->where('is_synced', '=', 0)
                    ->whereBetween('checkup_date', [$start_date, $end_date])
                    ->whereIn('booking_status', $bookingStatus); /*should not be pending & rescheduled status*/

            // if(!empty($modified_date)) {
            //     $details->whereDate('updated_at',$modified_date);
            // }

            $details = $details->paginate($pagination);
            $finalData = $details->transform(function($result) {
                if (isset($result->patient->patientHasManyHisMapping) && (!$result->patient->patientHasManyHisMapping->isEmpty())) {
                    $result->patient->patientHasManyHisMapping = $result->patient->patientHasManyHisMapping->filter(function($q) use($result) {
                        return $q->woodlands_hospital_code == $result->hospital->woodlands_hospital_code;
                    });
                } else {
                    $result->patient->patientHasManyHisMapping = null;
                }
                return $result;
            });

            $pagination = $this->generatePaginationDetails($details, $passedDataAction);
            $response['pagination'] = $pagination;
            $response['data'] = $finalData;
            if(isset($params['debug']) && ($params['debug'] == 1)) {
                $response['debug'] = 1;
            }
            return $response;
        } catch (\Exception $ex) {
            // logger()->error($ex->getCode() . '-' . $ex->getMessage());
            abort(400, $ex->getMessage());
        }
    }

    public function generatePaginationDetails($details, $passedDataAction) {
        $prev = NULL;
        $next = NULL;

        if (!empty($details->previousPageUrl())) {
            $prev = $details->previousPageUrl();
            if (isset($passedDataAction['isStartDate'])) {
                $prev = $prev . '&appointment_date=' . $passedDataAction['isStartDate'];
            }
            if (isset($passedDataAction['isHospitalCode'])) {
                $prev = $prev . '&hospital_code=' . $passedDataAction['isHospitalCode'];
            }
            if (isset($passedDataAction['isModifiedDate'])) {
                $prev = $prev . '&modified_date=' . $passedDataAction['isModifiedDate'];
            }

        }

        if (!empty($details->nextPageUrl())) {
            $next = $details->nextPageUrl();
            if (isset($passedDataAction['isStartDate'])) {
                $next = $next . '&appointment_date=' . $passedDataAction['isStartDate'];
            }
            if (isset($passedDataAction['isHospitalCode'])) {
                $next = $next . '&hospital_code=' . $passedDataAction['isHospitalCode'];
            }
            if (isset($passedDataAction['isModifiedDate'])) {
                $next = $next . '&modified_date=' . $passedDataAction['isModifiedDate'];
            }
        }

        $link = ['prev' => $prev, 'next' => $next];
        $responseArray = [
            'total' => $details->total(),
            'per_page' => $details->perPage(),
            'current_page' => $details->currentPage(),
            'total_pages' => $details->lastPage(),
            'links' => $link,
        ];
        return $responseArray;
    }

    public function create($request) {
        $testResult = $this->testResult;

        DB::beginTransaction();
        if ($request->has('test_booking_code')) {
            $testResult->test_booking_code = $request->test_booking_code;
            $test_id=TestBooking::where('test_booking_code',$request->test_booking_code)->select('test_id')->first();
            $testResult->test_id=$test_id->test_id;
        }

        if($request->has('service_code') && $request->has('major_code') && $request->has('minor_code')){
            $testResult->service_code = $request->service_code;
            $testResult->major_code = $request->major_code;
            $testResult->minor_code = $request->minor_code;
            $test=Test::where('service_code',$request->service_code)->where('major_code',$request->major_code)->where('minor_code',$request->minor_code)->select('id')->first();
            $testResult->test_id=$test->id;
        }

        if ($request->has('uhid')) {
            $testResult->uhid = $request->uhid;
        }
        if ($request->has('ref_doctor_gid')) {
            $testResult->ref_doctor_gid = $request->ref_doctor_gid;
        }
        if ($request->has('auth_doctor_gid')) {
            $testResult->auth_doctor_gid = $request->auth_doctor_gid;
        }
        if ($request->has('test_result')) {
            $testResult->test_result = $request->test_result;
        }
        if ($request->has('test_result_date')) {
            $testResult->test_result_date = $request->test_result_date;
        }
        if($request->has('min_value')){
            $testResult->min_value = $request->min_value;
        }
        if($request->has('max_value')){
            $testResult->max_value = str_replace('-', ' ', $request->max_value);
        }
        if($request->has('units')){
            $testResult->units = $request->units;
        }
        $testResult->save();
        DB::commit();
        return $testResult;

    }
}
