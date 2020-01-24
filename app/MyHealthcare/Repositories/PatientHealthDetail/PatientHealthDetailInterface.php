<?php

namespace App\MyHealthcare\Repositories\PatientHealthDetail;

interface PatientHealthDetailInterface
{
    public function create($request);

    public function update($patientId, $request);

    public function delete($id);

    public function restore($patientId);
}
