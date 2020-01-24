<?php

namespace App\MyHealthcare\Repositories\Hospital;

interface HospitalInterface
{
    public function getAll($keyword = null);

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);

    public function getHospitalSpecialityIds($hospital);

    public function getList();

    public function getCount();

    public function getHospitalSpeciality($hospital_id);

    public function getListByDoctor($doctorId);
    
    public function getHospitals($request);

    public function getHospitalsByCity($cityId);

    public function findByCode($code);
}
