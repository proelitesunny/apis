<?php

namespace App\MyHealthcare\Repositories\MedicineUsage;

interface MedicineUsageInterface
{
    public function getAll($keyword = Null);

    public function create($request);

    public function find($id);

    public function update($id, $request);

    public function getMedicineUsageList();
}
