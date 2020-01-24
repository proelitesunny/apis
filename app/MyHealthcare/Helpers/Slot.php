<?php

namespace App\MyHealthcare\Helpers;

class Slot
{
    public function getTimeSlotByObject($timeSchedules)
    {
        foreach ($timeSchedules as $timeSchedule) {
            if (isset($timeSchedule->timeSlots)) {
                foreach ($timeSchedule->timeSlots as $timeSlot) {
                    $startTime = strtotime($timeSlot->start_time);
                    $endTime = strtotime($timeSlot->end_time);

                    $breakTime = $timeSlot->break_time;

                    while ($startTime <= $endTime) {
                        $timeSlot->setSlotAttribute(date("H:i", $startTime));
                        $startTime = strtotime('+' . $breakTime . ' minutes', $startTime);
                    }
                }
            }
        }
    }
}