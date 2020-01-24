<?php

namespace App\MyHealthcare\Repositories\DoctorSchedule;

interface DoctorScheduleInterface
{
	public function getAvailableSlotList($request);
	
	public function getBookingList($request);
}