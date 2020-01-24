<?php

namespace App\MyHealthcare\Repositories\PatientPreference;

use App\Models\PatientPreference;

class PatientPreferenceRepository implements PatientPreferenceInterface
{
	/**
	 * @var PatientPreference
	 */
	private $patientPreference;

	/**
	 * PatientPreferenceRepository constructor.
	 * @param PatientPreference $patientPreference
	 */
	public function __construct(PatientPreference $patientPreference) {
		$this->patientPreference = $patientPreference;
	}


    public function create($request)
    {
        $patientPreference = $this->patientPreference->where('patient_id', $request->input('patient_id'))->where('setting', $request->input('setting'))->first();

        $patientPreference = is_null($patientPreference)?$this->patientPreference:$patientPreference;

        $this->buildObject($request, $patientPreference);

        $patientPreference->save();

        return $patientPreference;
    }


    public function update($request)
    {
        $patientPreference = $this->patientPreference->find($request->get('setting_id'));

        $this->buildObject($request, $patientPreference);
//
        $patientPreference->save();

        return $patientPreference;

    }

    private function buildObject($request, $patientPreference)
    {
        $patientPreference->patient_id = $request->has('patient_id') ? $request->input('patient_id') : null;

        $patientPreference->setting = $request->has('setting') ? $request->input('setting') : null;

        $patientPreference->values = $request->has('values') ? $request->input('values') : null;
    }

    public function delete($id)
    {
        $patientPreference = $this->patientPreference->find($id);
        $patientPreference->delete();
    }

    public function getPatientPreference($patientId)
    {
        $patientPreference = $this->patientPreference->where('patient_id', $patientId)->get();

        $patientPreference = $patientPreference->mapWithKeys(function($preference) {
            return [$preference->setting => $preference->values];
        })->toArray();

        return $patientPreference;
    }
}
