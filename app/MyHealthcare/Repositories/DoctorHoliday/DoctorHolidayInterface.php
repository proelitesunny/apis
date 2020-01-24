<?php

namespace App\MyHealthcare\Repositories\DoctorHoliday;

interface DoctorHolidayInterface
{
    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @param $keyword
     * @return mixed
     */
    public function searchByKeyword($keyword);

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $params
     * @return mixed
     */
    public function create($params);

    /**
     * @param $id
     * @param $params
     * @return mixed
     */
    public function update($id, $params);

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * @param $doctorId
     * @return mixed
     */
    public function getByDoctor($doctorId);
}
