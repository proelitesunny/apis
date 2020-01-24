<?php

namespace App\MyHealthcare\Repositories\InsuranceTieUp;

use App\Models\InsuranceTieUp;
use App\MyHealthcare\Helpers\Asset;

class InsuranceTieUpRepository implements InsuranceTieUpInterface
{
	/**
	 * @var InsuranceTieUp
	 */
	private $insuranceTieUp;

	/**
     * @var Asset
     */
    private $asset;

	/**
	 * InsuranceTieUpRepository constructor.
	 * @param InsuranceTieUp $insuranceTieUp
	 */
	public function __construct(InsuranceTieUp $insuranceTieUp, Asset $asset) {
		$this->insuranceTieUp = $insuranceTieUp;
		$this->asset = $asset;
	}

	public function getAll($keyword = null)
    {
        if (!$keyword) {
            return $this->insuranceTieUp->orderBy('id','desc')->paginate(10);
        }

        return $this->insuranceTieUp->with('pages')->whereHas('pages', function($q) use($keyword){ $q->where('title','Like','%'.$keyword.'%'); })
        ->orderBy('id','DESC')
        ->paginate(10);
    }

    public function find($id)
    {
        return $this->insuranceTieUp->find($id);
    }

	public function create($params)
    {
        $insuranceTieUp = $this->insuranceTieUp;

        $insuranceTieUp->image = isset($params['image']) ? $this->asset->storeAsset('insuranceTieUp', 'pages', $params['image']) : null;

        $insuranceTieUp->save();

        return $insuranceTieUp;
    }

    public function update($id, $params)
    {
        
        $lastFile = null;

        $insuranceTieUp = $this->insuranceTieUp->find($id);

        if (isset($params['image']) && $params['image'] != '') {
            $lastFile = $insuranceTieUp->image;

            $insuranceTieUp->image = $this->asset->storeAsset('insuranceTieUp', 'pages', $params['image']);
        }

        $insuranceTieUp->save();

        if ($lastFile) {
            $this->asset->deleteAsset($lastFile);
        }

        return $insuranceTieUp;
    }

    public function delete($id)
    {
        $insuranceTieUp = $this->insuranceTieUp->find($id);
        $insuranceTieUp->delete();
    }

    public function getCount()
    {
        return $this->insuranceTieUp->count();
    }
}
