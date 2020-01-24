<?php

namespace App\MyHealthcare\Repositories\Speciality;

use App\MyHealthcare\Helpers\Asset;
use App\MyHealthcare\Helpers\GenerateCode;
use App\Models\Speciality;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SpecialityRepository implements SpecialityInterface
{
    /**
     * @var Speciality
     */
    private $speciality;

    /**
     * @var GenerateCode
     */
    private $generateCode;

    /**
     * @var Asset
     */
    private $asset;


    public function __construct(Speciality $speciality, GenerateCode $generateCode, Asset $asset)
    {
        $this->speciality = $speciality;
        $this->generateCode = $generateCode;
        $this->asset = $asset;
    }

    /**
     * @param null $keyword
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll($keyword = null)
    {
        if (!$keyword) {
            return $this->speciality->orderBy('updated_at','DESC')
                        ->paginate(10);
        }
        return $this->speciality->where('name', 'LIKE', '%'.$keyword.'%')
            ->orWhere('speciality_code', 'LIKE', '%'.$keyword.'%')
            ->orderBy('id','DESC')
            ->paginate(10);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->speciality->with('specialityDetails')->findOrFail($id);
    }

    public function create($params)
    {
        $speciality = $this->speciality;

        $speciality->speciality_code = $this->generateCode->generateCode(
            $this->speciality,
            'speciality_code',
            'SPECID'
        );

        //$speciality->title = $params['title'];

        $speciality->name = $params['name'];

        //$speciality->description = $params['description'];

        $speciality->icon = $this->asset->storeAsset('specialities', 'specialities', $params['icon']);

        /*$speciality->speciality_image = isset($params['speciality_image']) ?
            $this->asset->storeAsset('specialities', 'specialities', $params['speciality_image']) :
            null;*/

        //$speciality->video_url = isset($params['video_url']) ? $params['video_url'] : null;

        $speciality->created_by = Auth::id();
        $speciality->updated_by = Auth::id();

        $speciality->save();

        return $speciality;
    }

    public function update($id, $params)
    {
        $lastFile = null;

        $speciality = $this->speciality->find($id);

        $speciality->name = $params['name'];

        if (isset($params['icon']) && $params['icon'] != '') {
            $lastFile = $speciality->icon;
            $speciality->icon = $this->asset->storeAsset('specialities', 'specialities', $params['icon']) ;
        }

        $speciality->updated_by = Auth::id();

        $speciality->save();

        if ($lastFile) {
            $this->asset->deleteAsset($lastFile);
        }

        return $speciality;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $speciality = $this->speciality->find($id);
        $speciality->delete();
        return true;
    }

    public function getList()
    {
        return $this->speciality->pluck('name', 'id');
    }

    public function getHospitalSpecialities($hospitalId)
    {
        /*return $this->speciality->with(['hospitalSpecialities' => function($query) use($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }])->pluck('name', 'id');*/

        return $this->speciality->whereHas('hospitalSpecialities', function($query) use ($hospitalId){
            $query->where('hospital_id', $hospitalId);
        })->pluck('name','id');
    }

    public function getCount(){
        return $this->speciality->count();
    }

    public function getAllSpecialities()
    {
        $specialities = $this->speciality->paginate(config('api.aggregator_api.items_per_page'));

        // if ($specialities->total() === 0) {
        //     abort(400, trans('errors.SPECIALITY_101'));
        // }

        return $specialities;
    }

    public function getAllTrash($keyword = null)
    {
        if (!$keyword) {
            return $this->speciality->onlyTrashed()->paginate(10);
        }
        return $this->speciality->onlyTrashed()
            ->where('name', 'LIKE', '%'.$keyword.'%')
            ->orWhere('speciality_code', 'LIKE', '%'.$keyword.'%')
            ->paginate(10);
    }

    public function restore($id)
    {
        $speciality = $this->speciality->withTrashed()->find($id);
        $speciality->deleted_at = NULL;
        $speciality->save();

        return $speciality;
    }

    public function uploadImage($params)
    {
        //dd($params['speciality_image']);
        $speciality_details=null;
        $lastFile=null;

        $baseUploadPath = config('constants.upload_path');
        
        if (isset($params['speciality_image']) && $params['speciality_image'] != '') {
            //$lastFile = $speciality->speciality_image;
            $speciality_details = $this->asset->storeAsset('specialitiesDetails', 'specialities', $params['speciality_image']) ;
            if(isset($params['hdn_speciality_image']) && $params['hdn_speciality_image'] != ''){
                $lastFile = $params['hdn_speciality_image'];
            }
 
        }
        if ($lastFile) {
            if(file_exists($baseUploadPath.$lastFile)){
                unlink($baseUploadPath.$lastFile);
            }
        }
        return $speciality_details;
    }

    public function deleteImage($params){
        
        //dd($params['speciality_image']);
        $lastFile=$params['speciality_image'];
        $baseUploadPath = config('constants.upload_path');

        if ($lastFile) {
           if(file_exists($baseUploadPath.$lastFile)){
                unlink($baseUploadPath.$lastFile);
            }
        }
    }
}
