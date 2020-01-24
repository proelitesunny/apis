<?php

namespace App\MyHealthcare\Repositories\Patient;

interface PatientInterface
{
    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function isVerified($patientId);
}
