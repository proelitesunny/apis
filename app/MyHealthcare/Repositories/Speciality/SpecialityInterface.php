<?php

namespace App\MyHealthcare\Repositories\Speciality;

interface SpecialityInterface
{
    public function getAll($keyword = null);

    public function find($id);

    public function create($params);

    public function update($id, $params);

    public function delete($id);

    public function getList();

    public function getHospitalSpecialities($hospitalId);

    public function getCount();

    public function getAllSpecialities();

    public function getAllTrash($keyword = null);

    public function restore($id);

    public function uploadImage($params);

    public function deleteImage($params);
}
