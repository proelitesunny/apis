<?php

namespace App\MyHealthcare\Repositories\BlockDoctorSlot;

use App\Models\BlockDoctorSlot;

class BlockDoctorSlotRepository implements BlockDoctorSlotInterface
{
	/**
	 * @var BlockDoctorSlot
	 */
	private $blockDoctorSlot;

	/**
	 * BlockDoctorSlotRepository constructor.
	 * @param BlockDoctorSlot $blockDoctorSlot
	 */
	public function __construct(BlockDoctorSlot $blockDoctorSlot)
    {
		$this->blockDoctorSlot = $blockDoctorSlot;
	}

    /**
     * @param $doctorId
     * @param $params
     * @return BlockDoctorSlot
     */
    public function block($doctorId, $params)
    {
        foreach ($params['data'] as $param) {
            foreach ($param['slots'] as $slot) {
                $blockDoctorSlot = $this->blockDoctorSlot->newInstance();

                if ($blockDoctorSlot->where('doctor_id', $doctorId)
                    ->where('doctor_session_slot_id', $slot)
                    ->where('block_date', $param['date'])
                    ->where('is_blocked', false)
                    ->first()
                ) {

                    $blockDoctorSlot = $blockDoctorSlot->where('doctor_id', $doctorId)
                        ->where('doctor_session_slot_id', $slot)
                        ->where('block_date', $param['date'])
                        ->where('is_blocked', false)
                        ->first();

                    $blockDoctorSlot->is_blocked = true;

                } else {
                    $blockDoctorSlot->doctor_id = $doctorId;
                    $blockDoctorSlot->doctor_session_slot_id = $slot;
                    $blockDoctorSlot->block_date = $param['date'];
                }

                $blockDoctorSlot->save();
            }
        }

        return $this->blockDoctorSlot->where('doctor_id', $doctorId)
            ->where('block_date', '>=', date("Y-m-d"))
            ->where('is_blocked', false)
            ->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function unblock($doctorId, $params)
    {
        foreach ($params['data'] as $param) {
            foreach ($param['slots'] as $slot) {
                $blockDoctorSlot = $this->blockDoctorSlot->newInstance();

                $blockedSlot = $blockDoctorSlot->where('doctor_id', $doctorId)
                    ->where('doctor_session_slot_id', $slot)
                    ->where('block_date', $param['date'])
                    ->where('is_blocked', true)
                    ->first();

                $blockedSlot->is_blocked = false;

                $blockedSlot->save();
            }
        }

        return true;
    }
}
