<?php

namespace App\MyHealthcare\Repositories\Patient;

use App\MyHealthcare\Repositories\MasterPatientIndex\MasterPatientIndexInterface;
use App\MyHealthcare\Repositories\PatientHealthDetail\PatientHealthDetailInterface;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientRepository implements PatientInterface
{
	/**
	 * @var Patient
	 */
	private $patient;

    private $patientHealthDetails;

    private $masterPatientIndex;

	/**
	 * PatientRepository constructor.
	 * @param Patient $patient
	 */
	public function __construct(
	    Patient $patient,
        PatientHealthDetailInterface $patientHealthDetails,
        MasterPatientIndexInterface $masterPatientIndex
    ) {
		$this->patient = $patient;

        $this->patientHealthDetails = $patientHealthDetails;

        $this->masterPatientIndex = $masterPatientIndex;
	}

    public function find($id)
    {
        return $this->patient->with('patientHealthDetails')->findOrFail($id);
    }

    public function create($request)
    {
        $patient = $this->patient;
        $registerPatient = null;

        // Check for uhid and facility_code
        if ($request->has('uhid') && $request->has('facility_code') && $request->header('aggregatorType') === 'practo') {
            $patientHisMapping = \App\Models\PatientHisMapping::where('fortis_uhid', $request->uhid)->where('fortis_hospital_code', $request->facility_code)->first();
            if (isset($patientHisMapping))
                return $this->patient->findOrFail($patientHisMapping->patient_id);
        }

        //if patient is already registered with is verified flag false, return patient object
        // $unverifiedPatient = $patient->whereHas('masterPatientIndex', function($query) use ($request) {
        //     $query->whereMobileNo($request->mobile_no)->whereIsVerified(0);
        // })->first();

        $unverifiedMasterPatientIndex = \App\Models\MasterPatientIndex::whereMobileNo($request->mobile_no)->whereIsVerified(0)->first();
        $unverifiedMasterPatientIndexId = ($unverifiedMasterPatientIndex)?$unverifiedMasterPatientIndex->id:null;
        $unverifiedPatient = $patient->where('master_indices_id', $unverifiedMasterPatientIndexId)->first();

        if (isset($unverifiedPatient))
            return $this->update($unverifiedPatient->id, $request);

        //if patient is already registered with is verified flag true, throw exception
        // $verifiedPatient = $patient->whereHas('masterPatientIndex', function($query) use ($request) {
        //     $query->whereMobileNo($request->mobile_no)->whereIsVerified(1);
        // })->first();

        $verifiedMasterPatientIndex = \App\Models\MasterPatientIndex::whereMobileNo($request->mobile_no)->whereIsVerified(1)->first();
        $verifiedMasterPatientIndexId = ($verifiedMasterPatientIndex)?$verifiedMasterPatientIndex->id:null;
        $verifiedPatient = $patient->where('master_indices_id', $verifiedMasterPatientIndexId)->get();

        if ($verifiedPatient->count() > 0) {

            // Check If Aggregator Patient Exist
            $aggregatorPatient = $verifiedPatient->filter(function($vPatient, $key){
                return $vPatient->getAttributes()['create_source'] == config('constants.CREATION_SOURCE_INTERNAL')['aggregator_'.strtolower(request()->header('aggregatorType'))];
            })->first();

            if (!is_null($aggregatorPatient))
                return $this->update($aggregatorPatient->id, $request);

            $registerPatient = $verifiedPatient->first()->masterPatientIndex;
        }

        DB::beginTransaction();

        $registerPatient =  is_null($registerPatient)?$this->masterPatientIndex->create($request):$registerPatient;

        $patient->master_indices_id = $registerPatient->id;

        $this->buildObject($request, $patient);

        $patient->created_by = Auth::id();

        $patient->isd_code = $request->get('isd_code');
        $patient->mobile_no = $request->get('mobile_no');

        $patient->save();

        DB::commit();

        return $patient;
    }

    /**
     * @param $request Request
     * @param $patient
     */
    private function buildObject($request, $patient)
    {
        if ($request->has('parent_id')) {
            $patient->parent_id = ($request->parent_id == 0)?null:$request->parent_id;
        }

        if ($request->has('first_name')) {
            $patient->first_name = $request->first_name;
        }

        if ($request->has('middle_name')) {
            $patient->middle_name = $request->middle_name;
        }

        if ($request->has('last_name')) {
            $patient->last_name = $request->last_name;
        }

        if ($request->has('email')) {
            $patient->email = $request->email;
        }

        if ($request->has('emergency_contact')) {
            $patient->emergency_contact = $request->emergency_contact;
        }

        if ($request->has('is_active')) {
            $patient->is_active = $request->is_active;
        }

        if($request->has('dob')){

            $patient->dob = $request->get('dob');
        }

        if ($request->has('guardian_name')) {
            $patient->guardian_name = $request->guardian_name;
        }

        if ($request->has('blood_group')) {
            $patient->blood_group = $request->blood_group;
        }

        if ($request->has('relation')) {
            $patient->relation = $request->relation;
        }

        if ($request->has('address')) {
            $patient->address = $request->get('address');
        }

        if ($request->has('address_2')) {
            $patient->address_2 = $request->address_2;
        }

        if ($request->has('latitude')) {
            $patient->latitude = $request->latitude;
        }

        if ($request->has('longitude')) {
            $patient->longitude = $request->longitude;
        }

        if ($request->has('country_id')) {
            $patient->country_id = $request->country_id;
        }

        if ($request->has('state_id')) {
            $patient->state_id = $request->state_id;
        }

        if ($request->has('city_id')) {
            $patient->city_id = $request->city_id;
        }

        if ($request->has('pin_code')) {
            $patient->pin_code = $request->pin_code;
        }

        if ($request->has('land_line_isd_code')) {
            $patient->land_line_isd_code = $request->land_line_isd_code;
        }

        if ($request->has('land_line_city_code')) {
            $patient->land_line_city_code = $request->land_line_city_code;
        }

        if ($request->has('land_line')) {
            $patient->land_line = $request->land_line;
        }

        if ($request->has('gender')) {
            $patient->gender = $request->gender;
        }

        if ($request->has('id_type')) {
            $patient->id_type = $request->id_type;
            $patient->id_number = base64_encode($request->id_number);

        }

        if ($request->has('isd_code')) {
            $patient->isd_code = $request->isd_code;
        }

        if($request->has('patient_type'))
        {
            $patient->patient_type = $request->patient_type;
        }

        if ($request->has('allergies_type')) {
            $patient->allergies_type = $request->allergies_type;
        }

        if ($request->has('allergens')) {
            $patient->allergens = $request->allergens;
        }

        if ($request->has('medical_history')) {
            $patient->medical_history = $request->medical_history;
        }

        if ($request->has('family_medical_history')) {
            $patient->family_medical_history = $request->family_medical_history;
        }

        if ($request->has('miscellaneous_reports')) {
            $patient->miscellaneous_reports = $request->miscellaneous_reports;
        }

        if ($request->has('treatment_history')) {
            $patient->treatment_history = $request->treatment_history;
        }

        if ($request->has('imp_note')) {
            $patient->imp_note = $request->imp_note;
        }

        if ($request->has('create_source')) {
            $patient->create_source = $request->create_source;
        }

        $patient->updated_by = Auth::id();
    }

    public function update($id, $request)
    {
        $patient = $this->patient->findOrFail($id);

        $this->buildObject($request, $patient);

        if($request->hasFile('profile_picture')){

            // $patient->profile_picture = $this->asset->storeAsset('patients', 'patients', $request->file('profile_picture'));
            $patient->profile_picture = $this->uploadImages($request->only(['profile_picture']));
        }

        $patient->data_source = 0;

        $masterPatientUpdate = $this->masterPatientIndex->update($patient->master_indices_id, $request);

        $patient->save();

        return $patient;
    }

    public function isVerified($patientId)
    {
        try {
            $activePatient = $this->patient->where('id', $patientId)
            ->whereIsActive(1)
            // ->whereCreateSource(config('constants.CREATION_SOURCE_INTERNAL')['aggregator_'.strtolower(request()->header('aggregatorType'))])
            ->firstOrFail();

            $verifiedMasterPatientIndex = \App\Models\MasterPatientIndex::where('id', $activePatient->master_indices_id)->whereIsVerified(1)->firstOrFail();

            return $activePatient;

            // return $this->patient->whereHas('masterPatientIndex', function($query) {
            //     $query->whereIsVerified(1);
            // })->where('id', $patientId)
            // ->whereIsActive(1)
            // ->firstOrFail();
        } catch (\Exception $e) {
            return false;
        }
    }
}
