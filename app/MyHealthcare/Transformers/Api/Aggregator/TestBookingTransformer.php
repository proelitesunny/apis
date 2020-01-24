<?php

namespace App\MyHealthcare\Transformers\Api\Aggregator;

use App\MyHealthcare\Transformers\Api\BaseTransformer;
use App\MyHealthcare\Helpers\PaymentHash;

class TestBookingTransformer extends BaseTransformer {
    public function getTestAppointmentDetailsResponse($details){

        if (!$details['data']->isEmpty()) {
            $responseArray['test_appointments'] = array();
            $responseArray['pagination'] = $details['pagination'];

            foreach ($details['data'] as $data) {
                $responseData = array();
                $uhid = "";
                $refundStatus = "";

                // $transaction = $data->transactions->first();
                $transaction = $data->transactionLatestFirst();
                if (isset($details['debug']) && (!empty($details['debug'] == 1))) {
                    $transaction = $data->transactionLatestFirst();
                }
                $patient = $data->patient;
                $doctor = $data->doctor;
                $hospital = $data->hospital;
                //$refund = isset($transaction->refund) ? $transaction->refund : null;
                $refund = \App\Models\TestRefund::where('test_transaction_id', $transaction->id)->first();
                if ((isset($patient->patientHasManyHisMapping)) && (!($patient->patientHasManyHisMapping->isEmpty()))) {
                    $uhid = $patient->patientHasManyHisMapping->first()->woodlands_uhid;
                }

                if (!empty($refund)) {

                    $refundStatus = $refund->status;
                }
                $responseData['id'] = $data->id; // Booking id
                $responseData['booking_code'] = $data->test_booking_code;
                $responseData['appointment_date'] = $data->checkup_date;
                $responseData['booking_datetime'] = \Carbon\Carbon::parse($data->updated_at)->format('Y-m-d H:i:s');
                $responseData['modified_date'] = \Carbon\Carbon::parse($data->updated_at)->format('Y-m-d');
                $responseData['start_time'] = isset($data->testHospitalMapping->start_time)?$data->testHospitalMapping->start_time:"";
                $responseData['end_time'] = isset($data->testHospitalMapping->end_time)?$data->testHospitalMapping->end_time:"";
                $responseData['is_synced'] = $data->is_synced;
                $responseData['patient_id'] = $patient->id; // Patient Id
                $responseData['uhid'] = (!empty($uhid)) ? $uhid : ""; // patient his mapping woodlands_uhid
                $responseData['first_name'] = $patient->first_name;
                $responseData['last_name'] = (!empty($patient->last_name)) ? $patient->last_name : "";
                $responseData['mobile_no'] = $patient->mobile_no;
                $responseData['email'] = (!empty($patient->email)) ? $patient->email : "";
                $responseData['address'] = (!empty($patient->address)) ? $patient->address : "";
                $responseData['address_2'] = (!empty($patient->address_2)) ? $patient->address_2 : "";
                $responseData['pin_code'] = (!empty($patient->pin_code)) ? $patient->pin_code : "";
                $responseData['dob'] = (!empty($patient->dob))?$patient->dob:"";
                $responseData['gender'] = (isset($patient->gender))?config('constants.SINGLE_GENDER_ENUM')[$patient->gender]:"";
                $responseData['marital_status'] = (isset($patient->marital_status))?config('constants.SINGLE_MARITAL_ENUM')[$patient->marital_status]:"";
                $responseData['hospital_code'] = $hospital->woodlands_hospital_code;

                // $responseData['doctor_fees'] = $fees;
                $responseData['convenience_fees'] = $data->convenience_fees;
                $responseData['discount_amount'] = $data->discount_amount;
                $responseData['total_amount'] = $data->total_amount;


                $responseData['booking_status'] = $data->booking_status;


                $responseData['payment_status'] = $transaction->payment_status;
                $responseData['payment_mode'] = $transaction->payment_type;
                $responseData['payment_datetime'] = \Carbon\Carbon::parse($transaction->updated_at)->format('Y-m-d H:i:s');
                $responseData['transaction_number'] = (!empty($transaction->txn_number)) ? $transaction->txn_number : "";

                $responseData['refund_status'] = $refundStatus;

                $responseData['is_arrived'] = $data->is_patient_arrived;

                //Add coupon code in response.
                $couponCode = \App\Models\CouponCode::find($data->coupon_code_id);

                $responseData['coupon_code'] = ($couponCode) ? $couponCode->coupon_code : "";
                // $responseData['title'] = $data->tests->title;
                // $responseData['type'] = config('constants.TEST_TYPE')[$data->tests->test_type];
                $responseData['major_code'] = $data->test->major_code;
                $responseData['minor_code'] = $data->test->minor_code;
                $responseData['service_code'] = $data->test->service_code;
                // $responseData['service_description'] = unserialize($data->tests->description);

                $responseArray['test_appointments'][] = $responseData;
            }
            //	dd($responseArray);

            return json_encode($responseArray, JSON_UNESCAPED_SLASHES);
        } else {
            abort(400, trans('errors.APPOINTMENT_104'));
        }
    }
}
