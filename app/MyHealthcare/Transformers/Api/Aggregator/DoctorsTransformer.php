<?php

namespace App\MyHealthcare\Transformers\Api\Aggregator;
use App\Models\Doctor;
use App\Models\Hospital;

use App\MyHealthcare\Transformers\Api\BaseTransformer;

class DoctorsTransformer extends BaseTransformer {

    public function getAppointmentDetail($details) {
        if (!$details['data']->isEmpty()) {
            $responseArray['appointments'] = array();
            $responseArray['pagination'] = $details['pagination'];

            foreach ($details['data'] as $data) {
                $responseData = array();
                $uhid = "";
                $hospitalCityName = "";

                if ((isset($data->patient->patientHasManyHisMapping)) && (!($data->patient->patientHasManyHisMapping->isEmpty()))) {
                    $uhid = $data->patient->patientHasManyHisMapping->first()->fortis_uhid;
                }

                if ((isset($data->hospital->city)) && (!(empty($data->hospital->city->toArray())))) {
                    $hospitalCityName = $data->hospital->city->name;
                }
                $speciality = array();
                if (isset($data->doctor->doctorSpecialities) && (!empty($data->doctor->doctorSpecialities->toArray()))) {
                    foreach ($data->doctor->doctorSpecialities as $ds) {
                        $speciality[] = $ds->name;
                    }
                }

                $responseData['patient_id'] = $data->patient_id;
                $responseData['booking_id'] = $data->id;
                $responseData['booking_code'] = $data->booking_code;
                $responseData['booking_status'] = $data->booking_status;
                $responseData['fees'] = $data->total_amount;
                $responseData['uhid'] = (!empty($uhid)) ? $uhid : ""; // patient his mapping fortis_uhid
                $responseData['doctor_id'] = (!empty($data->doctor->id)) ? $data->doctor->id : "";
                $responseData['doctor_name'] = (!empty($data->doctor->first_name)) ? $data->doctor->first_name : "";
                $responseData['doctor_speciality'] = $speciality;
                $responseData['hospital_id'] = $data->hospital->id;

                $responseData['hospital_city_name'] = $hospitalCityName;
                $responseData['hospital_name'] = $data->hospital->name;
                $responseData['hospital_address'] = $data->hospital->address;
                $responseData['hospital_primary_contact'] = $data->hospital->primary_contact;
                $responseData['appointment_date'] = \Carbon\Carbon::parse($data->booking_date)->format('Y-m-d');
                $responseData['appointment_start_time'] = $data->start_time;
                $responseData['appointment_end_time'] = $data->end_time;

                $responseArray['appointments'][] = $responseData;
            }

            return $responseArray;
        } else {
            abort(400, trans('errors.APPOINTMENT_101'));
        }
    }

    public function getDoctorsDetail($details) {

        if (!$details['details']->isEmpty()) {
            $responseArray['doctors'] = array();
            $responseArray['pagination'] = array();

            $next = null;
            $prev = null;
            if (!empty($details['details']->previousPageUrl())) {
                $prev = $details['details']->previousPageUrl();
                if (!empty($details['hospital_name'])) {
                    $prev = $prev . '&hospital_name=' . $details['hospital_name'];
                }
                if (!empty($details['speciality_name'])) {
                    $prev = $prev . '&speciality_name=' . $details['speciality_name'];
                }
                if (!empty($details['city_name'])) {
                    $prev = $prev . '&city_name=' . $details['city_name'];
                }
            }
            if (!empty($details['details']->nextPageUrl())) {
                $next = $details['details']->nextPageUrl();
                if (!empty($details['hospital_name'])) {
                    $next = $next . '&hospital_name=' . $details['hospital_name'];
                }
                if (!empty($details['speciality_name'])) {
                    $next = $next . '&speciality_name=' . $details['speciality_name'];
                }
                if (!empty($details['city_name'])) {
                    $next = $next . '&city_name=' . $details['city_name'];
                }
            }
            $link = ['prev' => $prev, 'next' => $next];
            $responseArray['pagination'] = [
                'total' => $details['details']->total(),
                'per_page' => $details['details']->perPage(),
                'current_page' => $details['details']->currentPage(),
                'total_pages' => $details['details']->lastPage(),
                'links' => $link,
            ];
            foreach ($details['details'] as $data) {
                $responseData = array();
                $title = "";
                $designation = "";
                $gender = "";
                $speciality = array();
                $titleArray = config('constants.TITLE');
                $genderArray = config('constants.GENDER_ENUM');

                if (isset($titleArray[$data->doctor->title])) {
                    $title = $titleArray[$data->doctor->title];
                }
                if (isset($genderArray[$data->doctor->gender])) {
                    // $gender = $genderArray[$data->doctor->gender];
                    $gender = $data->doctor->gender;
                }
                $speciality = array();
                if (isset($data->doctor->doctorSpecialities) && (!empty($data->doctor->doctorSpecialities->toArray()))) {
                    foreach ($data->doctor->doctorSpecialities as $ds) {
                        $speciality[] = $ds->name;
                    }
                }


                $responseData['id'] = $data->doctor->id;
                $responseData['title'] = $title;
                $responseData['first_name'] = $data->doctor->first_name;
                $responseData['middle_name'] = (!empty($data->doctor->middle_name))?$data->doctor->middle_name:"";
                $responseData['last_name'] = (!empty($data->doctor->last_name))?$data->doctor->last_name:"";
                $responseData['designation'] = (!empty($data->designation))?$data->designation:"";
                $responseData['qualifications'] = (!empty($data->doctor->qualification))?strip_tags($data->doctor->qualification):"";
                $responseData['experience'] = (!empty($data->doctor->experience))?$data->doctor->experience:"";
                $responseData['gender'] = $gender;
                $responseData['is_active_on_portal'] = (isset($data->doctor->active_on_portal) && !empty($data->doctor->active_on_portal))?"Yes":"No";;
                $responseData['specialities'] = $speciality;
                $responseData['profile_image']= $this->imageUrl($data->doctor->profile_picture);
                $responseData['fees'] = $data->fees;
                $responseData['hospital_id'] = $data->hospital->id;
                $responseData['hospital_city_name'] = (!empty($data->hospital->city->name))?$data->hospital->city->name:"";
                $responseData['hospital_name'] = $data->hospital->name;
                $responseData['hospital_address'] = (!empty($data->hospital->address))?$data->hospital->address:"";
                $responseData['hospital_primary_contact'] = (!empty($data->hospital->primary_contact))?$data->hospital->primary_contact:"";
                $responseData['hospital_secondary_contact'] = (!empty($data->hospital->secondary_contact))?$data->hospital->secondary_contact:"";
                $responseData['hospital_emergency_contact'] = (!empty($data->hospital->emergency_contact))?$data->hospital->emergency_contact:"";
                $responseData['hospital_helpline_sos'] = (!empty($data->hospital->helpline_sos))?$data->hospital->helpline_sos:"";

                $responseArray['doctors'][] = $responseData;
            }
            return $responseArray;
        } else {
            abort(400, trans('errors.APPOINTMENT_101'));
        }
    }

    public function getDoctorsAvailableSlotDetail($details) {
        if (isset($details['slot_details']['slots']) && (!empty($details['slot_details']['slots']))) {

            if (($details['include_doctor'] == TRUE) && (isset($details['doctor_details'])) && (!empty($details['doctor_details']->toArray()))) {
                $title = "";
                $designation = "";
                $gender = "";
                $speciality = array();
                $titleArray = config('constants.TITLE');
                $genderArray = config('constants.GENDER_ENUM');
                if (isset($titleArray[$details['doctor_details']->doctor->title])) {
                    $title = $titleArray[$details['doctor_details']->doctor->title];
                }
                if (isset($genderArray[$details['doctor_details']->doctor->gender])) {
                    // $gender = $genderArray[$details['doctor_details']->doctor->gender];
                    $gender = $details['doctor_details']->doctor->gender;
                }
                $speciality = array();
                if (isset($details['doctor_details']->doctor->doctorSpecialities) && (!empty($details['doctor_details']->doctor->doctorSpecialities->toArray()))) {
                    foreach ($details['doctor_details']->doctor->doctorSpecialities as $ds) {
                        $speciality[] = $ds->name;
                    }
                }
                $msg  = "";
                if(isset($details['doctor_details']->hospital->payment_type) && $details['doctor_details']->hospital->payment_type ==1) {
                    $msg = trans('success.BOOKING_103');
                }
                $responseArray['doctor'] = array(
                "id" => $details['doctor_details']->doctor_id,
                "title" => $title,
                "first_name" => $details['doctor_details']->doctor->first_name,
                "middle_name" => (!empty($details['doctor_details']->doctor->middle_name))?$details['doctor_details']->doctor->middle_name:"",
                "last_name" => (!empty($details['doctor_details']->doctor->last_name))?$details['doctor_details']->doctor->last_name:"",
                "designation" => (!empty($details['doctor_details']->designation))?$details['doctor_details']->designation:"",
                "qualifications" => (!empty($details['doctor_details']->doctor->qualification))?strip_tags($details['doctor_details']->doctor->qualification):"",
                "gender" => $gender,
                "is_star_doctor" => $details['doctor_details']->doctor->is_star_doctor,
                "payment_type" => $details['doctor_details']->hospital->payment_type,
                "payment_message" => $msg,
                "specialities" => $speciality,
                "fees" => $details['doctor_details']->fees,
                "hospital_id" => $details['doctor_details']->hospital->id,
                "hospital_city_name" => (!empty($details['doctor_details']->hospital->city->name))?$details['doctor_details']->hospital->city->name:"",
                "hospital_name" => $details['doctor_details']->hospital->name,
                "hospital_address" => (!empty($details['doctor_details']->hospital->address))?$details['doctor_details']->hospital->address:"",
                "hospital_primary_contact" => (!empty($details['doctor_details']->hospital->primary_contact))?$details['doctor_details']->hospital->primary_contact:"",
                "hospital_secondary_contact" => (!empty($details['doctor_details']->hospital->secondary_contact))?$details['doctor_details']->hospital->secondary_contact:"",
                "hospital_emergency_contact" => (!empty($details['doctor_details']->hospital->emergency_contact))?$details['doctor_details']->hospital->emergency_contact:"",
                "hospital_helpline_sos" => (!empty($details['doctor_details']->hospital->helpline_sos))?$details['doctor_details']->hospital->helpline_sos:""

                );
            }
            $responseArray['slots'] = $details['slot_details']['slots'];
            return $responseArray;
        } else {
            abort(400, trans('errors.APPOINTMENT_101'));
        }
    }

}
