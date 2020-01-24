<?php

namespace App\MyHealthcare\Repositories\DoctorHoliday;

use App\Exceptions\LeaveAlreadyExists;
use App\Models\DoctorHoliday;

class DoctorHolidayRepository implements DoctorHolidayInterface
{
	/**
	 * @var DoctorHoliday
	 */
	private $doctorHoliday;

	/**
	 * DoctorHolidayRepository constructor.
	 * @param DoctorHoliday $doctorHoliday
	 */
	public function __construct(DoctorHoliday $doctorHoliday) {
		$this->doctorHoliday = $doctorHoliday;
	}

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->doctorHoliday->paginate(10);
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public function searchByKeyword($keyword)
    {
        return $this->doctorHoliday->paginate(10);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->doctorHoliday->findOrFail($id);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function create($params)
    {
        $this->checkLeaveExists($params['doctor_id'], $params['start_date'], $params['end_date']);

        $doctorHoliday = $this->doctorHoliday;

        foreach ($params as $key => $param) {
            $doctorHoliday->$key = $param;
        }

        $doctorHoliday->save();

        return $doctorHoliday;
    }

    /**
     * @param $id
     * @param $params
     * @return mixed
     */
    public function update($id, $params)
    {
        $doctorHoliday = $this->doctorHoliday->findOrFail($id);

        foreach ($params as $key => $param) {
            $doctorHoliday->$key = $param;
        }

        $doctorHoliday->save();

        return $doctorHoliday;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $doctorHoliday = $this->doctorHoliday->findOrFail($id);

        $doctorHoliday->delete();

        return true;
    }

    /**
     * @param $doctorId
     * @return mixed
     */
    public function getByDoctor($doctorId)
    {
        return $this->doctorHoliday->where('doctor_id', $doctorId)->get();
    }

    /**
     * @param $doctor
     * @param $date
     * @return mixed
     */
    private function checkLeaveExists($doctorId, $startDate, $endDate)
    {
         $holiday = $this->doctorHoliday->where('doctor_id', $doctorId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                    ->orWhere('end_date', '>=', $endDate);
            })->first();

         if ($holiday != null) {
             throw (new LeaveAlreadyExists('Leave already exists form '.$holiday->start_date.' to '.$holiday->end_date));
         }
    }
}
