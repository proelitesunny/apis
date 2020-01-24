<?php

namespace App\MyHealthcare\Repositories\BlockDoctorSlot;

interface BlockDoctorSlotInterface
{
    public function block($doctorId, $params);

    public function unblock($doctorId, $params);
}
