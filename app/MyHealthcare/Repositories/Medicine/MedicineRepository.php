<?php

namespace App\MyHealthcare\Repositories\Medicine;

use App\MyHealthcare\Helpers\GenerateCode;
use App\Models\Medicine;
use App\Models\MedicineUsage;

class MedicineRepository implements MedicineInterface
{
	/**
	 * @var Medicine
	 */
	private $medicine;

	private $generateCode;

	/**
	 * MedicineRepository constructor.
	 * @param Medicine $medicine
	 */
	public function __construct(Medicine $medicine, GenerateCode $generateCode) {
		$this->medicine = $medicine;

		$this->generateCode = $generateCode;
	}

    public function getAll($keyword = null)
    {
        return $keyword ? $this->medicine->where('medicine_code', 'LIKE', '%'. $keyword. '%')
            ->orwhere('name', 'LIKE', '%'. $keyword. '%')
            ->orWhere('type', 'LIKE', '%'. $keyword. '%')
            ->paginate(10) :
            $this->medicine->paginate(10);
    }

    public function find($id)
    {
        return $this->medicine->with('medicineUsage')->find($id);
    }

    public function create($request)
    {
        $medicine = $this->medicine;

        $medicine->medicine_code = $this->generateCode->generateCode($medicine, 'medicine_code', 'MEDID');

        $this->buildObject($request, $medicine);

        $medicine->save();

        $medicine->medicineUsage()->sync($request->get('medicine_usage'));

        return $medicine;
    }

    private function buildObject($request, $medicine)
    {
        $medicine->name = $request->get('name');

        $medicine->type = $request->get('type');

        $medicine->description = $request->get('description') ? $request->get('description') : null;

        $medicine->status = $request->get('status');
    }

    public function update($id, $request)
    {
        $medicine = $this->medicine->find($id);

        $this->buildObject($request, $medicine);

        $medicine->save();

        $medicine->medicineUsage()->sync($request->get('medicine_usage'));

        return $medicine;
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function getMedicineUsageIds($medicine)
    {
        $medicineUsageIds = [];

        foreach ($medicine->medicineUsage as $usage) {
            $medicineUsageIds[] = $usage->id;
        }

        return $medicineUsageIds;
    }
}
