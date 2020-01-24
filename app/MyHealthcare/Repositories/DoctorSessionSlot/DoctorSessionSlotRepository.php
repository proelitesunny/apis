<?php

namespace App\MyHealthcare\Repositories\DoctorSessionSlot;

use App\Models\DoctorSessionSlot;

class DoctorSessionSlotRepository implements DoctorSessionSlotInterface
{
	/**
	 * @var DoctorSessionSlot
	 */
	private $doctorSessionSlot;

	/**
	 * DoctorSessionSlotRepository constructor.
	 * @param DoctorSessionSlot $doctorSessionSlot
	 */
	public function __construct(DoctorSessionSlot $doctorSessionSlot)
    {
		$this->doctorSessionSlot = $doctorSessionSlot;
	}

    /**
     * @param $doctorScheduleSessionId
     * @param $params
     */
    public function create($doctorScheduleSessionId, $params)
    {
        foreach ($params as $slot) {
            $doctorSessionSlot = $this->doctorSessionSlot->newInstance();
            $doctorSessionSlot->doctor_schedule_session_id = $doctorScheduleSessionId;
            $doctorSessionSlot->time = $slot['time'];
            $doctorSessionSlot->visibility_a = $slot['visibility_a'];
            $doctorSessionSlot->visibility_c = $slot['visibility_c'];
            $doctorSessionSlot->save();
        }
    }

    public function getSessionBy($slotId)
    {
        $slot = $this->doctorSessionSlot->find($slotId);

        return $slot;
    }

    public function update($doctorScheduleSessionId, $params)
    {
        foreach ($params as $slot) {
            if (isset($slot['id']) && !empty($slot['id'])) {
                $doctorSessionSlot = $this->doctorSessionSlot->newInstance();
                $doctorSessionSlot = $doctorSessionSlot->find($slot['id']);

                $doctorSessionSlot->visibility_a = $slot['visibility_a'];
                $doctorSessionSlot->visibility_c = $slot['visibility_c'];
                $doctorSessionSlot->save();
            } else {
                $this->createSingleSlot($doctorScheduleSessionId, $slot);
            }
        }
    }

    private function createSingleSlot($doctorScheduleSessionId, $slot)
    {
        $doctorSessionSlot = $this->doctorSessionSlot->newInstance();
        $doctorSessionSlot->doctor_schedule_session_id = $doctorScheduleSessionId;
        $doctorSessionSlot->time = $slot['time'];
        $doctorSessionSlot->visibility_a = $slot['visibility_a'];
        $doctorSessionSlot->visibility_c = $slot['visibility_c'];
        $doctorSessionSlot->save();
    }
}
