<?php

namespace App\MyHealthcare\Repositories\PatientHealthDetail;

use App\Models\PatientHealthDetail;

class PatientHealthDetailRepository implements PatientHealthDetailInterface
{
	/**
	 * @var PatientHealthDetail
	 */
	private $patientHealthDetail;

	/**
	 * PatientHealthDetailRepository constructor.
	 * @param PatientHealthDetail $patientHealthDetail
	 */
	public function __construct(PatientHealthDetail $patientHealthDetail) {
		$this->patientHealthDetail = $patientHealthDetail;
	}


    public function create($request)
    {
        $patientHealthDetail = $this->patientHealthDetail;

        $this->buildObject($request, $patientHealthDetail);

        return $patientHealthDetail;
    }


    public function update($patientId, $request)
    {
        $patientHealthDetail = $this->patientHealthDetail->where('patient_id', $patientId)->first();

        $this->buildObject($request, $patientHealthDetail);
//
        $patientHealthDetail->save();

        return $patientHealthDetail;

    }

    private function buildObject($request, $patientHealthDetail)
    {
        if ($request->has('allergies_type')) {
            $patientHealthDetail->allergies_type = $request->allergies_type;
        }

        if ($request->has('allergens')) {
            $patientHealthDetail->allergens = $request->allergens;
        }

        if ($request->has('medical_history')) {
            $patientHealthDetail->medical_history = $request->medical_history;
        }

        if ($request->has('family_medical_history')) {
            $patientHealthDetail->family_medical_history = $request->family_medical_history;
        }

        if ($request->has('miscellaneous_reports')) {
            $patientHealthDetail->miscellaneous_reports = $request->miscellaneous_reports;
        }

        if ($request->has('treatment_history')) {
            $patientHealthDetail->treatment_history = $request->treatment_history;
        }

        if ($request->has('imp_note')) {
            $patientHealthDetail->imp_note = $request->imp_note;
        }

    }

    public function delete($id)
    {
        $patientHealth = $this->patientHealthDetail->find($id);
        $patientHealth->delete();
    }

    public function restore($patientId)
    {
        $patientHealthDetail = $this->patientHealthDetail->where('patient_id', $patientId)
                                ->onlyTrashed()
                                ->first();
        $patientHealthDetail->deleted_at = NULL;
        $patientHealthDetail->save();

        return $patientHealthDetail;
    }

}
