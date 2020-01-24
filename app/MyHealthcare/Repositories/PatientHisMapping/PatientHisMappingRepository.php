<?php

namespace App\MyHealthcare\Repositories\PatientHisMapping;

use App\MyHealthcare\Repositories\Hospital\HospitalInterface;
use App\Models\Patient;
use App\Models\PatientHisMapping;

class PatientHisMappingRepository implements PatientHisMappingInterface
{
    /**
     * @var PatientHisMapping
     */
    private $patientHisMapping;

    private $hospital;

    /**
     * PatientHisMappingRepository constructor.
     * @param PatientHisMapping $patientHisMapping
     */
    public function __construct(PatientHisMapping $patientHisMapping, HospitalInterface $hospital) {
        $this->patientHisMapping = $patientHisMapping;
        $this->hospital = $hospital;
    }

    public function create($patient)
    {
        $patienHisMapping = $this->patientHisMapping;

        $patienHisMapping->master_patient_indices_id = $patient->master_indices_id;
        $patienHisMapping->patient_id = $patient->id;

        $patienHisMapping->save();

        return $patienHisMapping;
    }

    public function makeMap($patientId, $hospitalId)
    {
        $hospital = $this->hospital->find($hospitalId);

        if ($this->patientHisMapping->where('patient_id', $patientId)->where('fortis_hospital_code', $hospital->fortis_hospital_code)->first()) {
            return null;
        }

        $patient = Patient::find($patientId);

        $patientHisMapping = $this->patientHisMapping->newInstance();

        $patientHisMapping->master_patient_indices_id = $patient->master_indices_id;

        $patientHisMapping->patient_id = $patientId;

        $patientHisMapping->fortis_hospital_code = $hospital->fortis_hospital_code;

        $patientHisMapping->save();
    }
}
