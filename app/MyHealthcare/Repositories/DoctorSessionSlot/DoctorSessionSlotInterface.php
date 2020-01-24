<?php

namespace App\MyHealthcare\Repositories\DoctorSessionSlot;

interface DoctorSessionSlotInterface
{
    public function create($doctorScheduleSessionId, $params);

    public function getSessionBy($slotId);

    public function update($doctorScheduleSessionId, $params);
}
