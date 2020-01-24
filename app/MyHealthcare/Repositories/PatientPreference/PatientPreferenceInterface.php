<?php

namespace App\MyHealthcare\Repositories\PatientPreference;

interface PatientPreferenceInterface
{
    public function create($request);

    public function update($request);

    public function delete($id);

    public function getPatientPreference($patientId);
}
