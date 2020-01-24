<?php

namespace App\MyHealthcare\Repositories\MasterPatientIndex;

interface MasterPatientIndexInterface
{
    public function find($request);

    public function create($request);

    public function update($masterId,$request);

    public function isPatientExist($user);

    public function validateLogin($request);

    public function setOtp($request);

    public function validateOtp($request);

    public function setPassword($request, $id);
}
