<?php

namespace App\MyHealthcare\Repositories\Medicine;

interface MedicineInterface
{
    public function getAll($keyword = null);

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);

    public function getMedicineUsageIds($medicine);
}
