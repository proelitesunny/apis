<?php

namespace App\MyHealthcare\Repositories\PatientHisMapping;

interface PatientHisMappingInterface
{
    public function create($patient);

    public function makeMap($patientId, $hospitalId);
}
