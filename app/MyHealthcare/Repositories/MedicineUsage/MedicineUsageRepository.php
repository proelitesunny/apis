<?php

namespace App\MyHealthcare\Repositories\MedicineUsage;

use App\MyHealthcare\Helpers\GenerateCode;
use App\Models\MedicineUsage;

class MedicineUsageRepository implements MedicineUsageInterface
{
	/**
	 * @var MedicineUsage
	 */
	private $medicineUsage;

    /**
     * @var GenerateCode
     */
	private $generateCode;

    /**
     * MedicineUsageRepository constructor.
     * @param MedicineUsage $medicineUsage
     * @param GenerateCode $generateCode
     */
	public function __construct(MedicineUsage $medicineUsage, GenerateCode $generateCode) {
		$this->medicineUsage = $medicineUsage;

		$this->generateCode = $generateCode;
	}

    public function getAll($keyword = Null)
    {
        return $keyword ? $this->medicineUsage->where('use_code', 'LIKE', '%'. $keyword. '%')
                          ->orwhere('name', 'LIKE', '%'. $keyword. '%')
                          ->paginate(10) :
            $this->medicineUsage->paginate(10);
    }

    public function find($id)
    {
        return $this->medicineUsage->find($id);
    }

    public function create($request)
    {
        $usage = $this->medicineUsage;

        $usage->use_code = $this->generateCode->generateCode($usage, 'use_code', 'USEID');

        $usage->name = $request->get('name');

        $usage->save();

        return $usage;
    }

    public function update($id, $request)
    {
        $usage = $this->find($id);

        $usage->name = $request->get('name');

        $usage->save();

        return $usage;
    }

    public function getMedicineUsageList()
    {
        return $this->medicineUsage->pluck('name', 'id');
    }


}
