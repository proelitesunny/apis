<?php

namespace App\MyHealthcare\Repositories\Insurance;

use App\MyHealthcare\Helpers\Asset;
use App\Models\Insurance;

class InsuranceRepository implements InsuranceInterface
{
	/**
	 * @var Insurance
	 */
	private $insurance;

	private $asset;
	/**
	 * InsuranceRepository constructor.
	 * @param Insurance $insurance
	 */
	public function __construct(Insurance $insurance, Asset $asset) {
		$this->insurance = $insurance;

		$this->asset = $asset;
	}

    public function create($request)
    {
      $insurance = $this->insurance;

      $this->buildObject($request, $insurance);

      $insurance->uploads = $request->hasFile('uploads') ? $this->asset->storeAsset('asset', 'insurance', $request->file('uploads')) : null;

      $insurance->save();

      return $insurance;
    }

    private function buildObject($request, $insurance)
    {
        $insurance->patient_id = $request->get('patient_id');

        $insurance->policy_number = $request->get('policy_number');

        $insurance->provider = $request->get('provider');

        $insurance->start_date = $request->get('start_date');

        $insurance->end_date = $request->get('end_date');

        return $insurance;
    }

    public function update($id, $request)
    {
        $insurance = $this->insurance->find($id);

        $insurance->uploads = $request->hasFile('uploads') ?
            $this->asset->storeAsset('assets', 'insurance', $request->file('uploads')) :
            $insurance->uploads;

        $this->buildObject($request, $insurance);

        $insurance->save();

        return $insurance;
    }

    public function find($id)
    {
        return $this->insurance->with('patient')->find($id);
    }

    public function delete($id)
    {

    }


}
