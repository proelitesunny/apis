<?php

namespace App\MyHealthcare\Repositories\Booking;

interface BookingInterface
{
    public function store($patientId, $doctorId, $request);

    public function find($id);

    public function updateAppointmentStatus($request);
}
