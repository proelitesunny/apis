<?php

namespace App\MyHealthcare\Repositories\HospitalHoliday;

interface HospitalHolidayInterface
{
	public function getAll($keyword = null);

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function getHospitalIds($hospital);

    public function delete($id);

    //public function getList();

    //public function getCount();
}
