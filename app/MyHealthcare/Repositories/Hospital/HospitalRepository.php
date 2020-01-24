<?php

namespace App\MyHealthcare\Repositories\Hospital;

use App\MyHealthcare\Helpers\Asset;
use App\MyHealthcare\Helpers\GenerateCode;
use App\MyHealthcare\Helpers\GeoLocation;
use App\Models\Hospital;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HospitalRepository implements HospitalInterface
{
	/**
	 * @var Hospital
	 */
	private $hospital;

    /**
     * @var GenerateCode
     */
	private $generateCode;

    /**
     * @var Asset
     */
	private $asset;

    /**
     * @var GeoLocation
     */
	private $getLocation;

    /**
     * HospitalRepository constructor.
     * @param Hospital $hospital
     * @param GenerateCode $generateCode
     * @param Asset $asset
     * @param GeoLocation $geoLocation
     */
	public function __construct(Hospital $hospital, GenerateCode $generateCode, Asset $asset, GeoLocation $geoLocation) {
		$this->hospital = $hospital;
        $this->generateCode = $generateCode;
        $this->asset = $asset;
        $this->getLocation = $geoLocation;
	}

    public function getAll($keyword = null)
    {
        return $keyword ?
        $this->hospital->where('hospital_code', 'LIKE', '%'.$keyword.'%')
            ->orWhere('name', 'LIKE', '%'.$keyword.'%')
            ->orWhere('address', 'LIKE', '%'.$keyword.'%')
            ->orWhere('email', 'LIKE', '%'.$keyword.'%')
            ->orderBy('id','DESC')
            ->paginate(10)
        :
        $this->hospital->orderBy('updated_by', 'DESC')->paginate(10);
    }

    public function find($id)
    {
        return $this->hospital->with('hospitalSpecialities')->findOrFail($id);
    }

    public function create($request)
    {
        $hospital = $this->hospital;

        $hospital->hospital_code = $this->generateCode->generateCode($this->hospital, 'hospital_code', 'HOSPID');

        $this->buildObject($request, $hospital);

        $hospital->hospital_image = $this->asset->storeAsset('hospitals', 'hospitals', $request->file('hospital_image'));

        $hospital->created_by = Auth::id();

        $hospital->save();

        $hospital->hospitalSpecialities()->attach($request->get('specialities'));

        return $hospital;
    }

    private function buildObject($request, $hospital)
    {
        $hospital->name = $request->get('name');

        $hospital->address = $request->get('address');

        $hospital->primary_contact = $request->get('primary_contact');

        $hospital->secondary_contact = $request->has('secondary_contact') ? $request->get('secondary_contact') : null;

        $hospital->emergency_contact = $request->get('emergency_contact');

       // $latLng = $this->getLocation->getLatLng($request->get('address'));

        $hospital->latitude = $request->get('latitude');

        $hospital->longitude = $request->get('longitude');

        $hospital->email = $request->get('email');

        $hospital->description = $request->has('description') ? $request->get('description') : null;

        $hospital->ambulance_sos = $request->get('ambulance_sos') ? $request->get('ambulance_sos') : null;

        $hospital->helpline_sos = $request->has('helpline_sos') ? $request->get('helpline_sos') : null;

        $hospital->blood_bank_sos = $request->has('blood_bank_sos') ? $request->get('blood_bank_sos') : null;

        $hospital->country_id = $request->get('country_id') ? $request->get('country_id') : null;

        $hospital->state_id = $request->get('state_id') ? $request->get('state_id') : null;

        $hospital->city_id = $request->get('city_id') ? $request->get('city_id') : null;

        $hospital->updated_by = Auth::id();
    }

    public function update($id, $request)
    {
        $lastFile = null;

        $hospital = $this->hospital->find($id);

        $this->buildObject($request, $hospital);

        if ($request->hasFile('hospital_image')) {
            $lastFile = $hospital->hospital_image;

            $hospital->hospital_image = $this->asset->storeAsset(
                'hospitals',
                'hospitals',
                $request->file('hospital_image')
            );
        }

        $hospital->save();

        $hospital->hospitalSpecialities()->sync($request->get('specialities'));

        if ($lastFile) {
            $this->asset->deleteAsset($lastFile);
        }

        return $hospital;
    }

    public function delete($id)
    {
        $hospital = $this->hospital->find($id);
        $hospital->delete();
        $hospital->hospitalSpecialities()->sync([]);
    }

    public function getHospitalSpecialityIds($hospital)
    {
        $hospitalSpecialitiesIds = [];

        foreach ($hospital->hospitalSpecialities as $speciality) {
            $hospitalSpecialitiesIds[] = $speciality->id;
        }

        return $hospitalSpecialitiesIds;
    }

    public function getList()
    {
        return $this->hospital->pluck('name', 'id');
    }

    public function getCount()
    {
        return $this->hospital->count();
    }

    public function getHospitalSpeciality($hospital_id)
    {
        //return $this->hospital->where('hospital_id')
    }

    public function getListByDoctor($doctorId)
    {
        return $this->hospital->whereHas('doctorHospital', function ($query) use ($doctorId) {
            $query->where('doctor_id', $doctorId);
        })->pluck('name', 'id');
    }

    public function getHospitals($request)
    {
        try {
            $lat = $request->input('lat');
            $lng = $request->input('lng');

            $circle_radius = config('api.aggregator_api.circle_radius');

            $fields = ''; $order_by = 'c.name';
            if (!empty($lat) && !empty($lng)) {
                $fields = ', (' . $circle_radius . ' * acos(cos(radians(' . $lat . ')) * cos(radians(h.latitude)) * cos(radians(h.longitude) - radians(' . $lng . ')) + sin(radians(' . $lat . ')) * sin(radians(h.latitude)))) AS distance';
                $order_by = 'distance';
            }

            $hospitals = DB::select('SELECT h.id, hospital_code, h.name, h.address, h.latitude, h.longitude, c.id as city_id, c.name as city'.$fields.' FROM hospitals h join cities c ON (h.city_id = c.id) ORDER BY '.$order_by);

            // if (empty($hospitals)) {
            //     abort(400, trans('errors.HOSPITAL_106'));
            // }

            return $hospitals;
        }
        catch(\Exception $e) {
            logger()->error($e->getMessage());
            abort(400, trans('errors.HOSPITAL_105'));
        }
    }

    public function getHospitalsByCity($cityId)
    {
        return $this->hospital->where('city_id', $cityId)->get();
    }

    public function findByCode($code)
    {
        return $this->hospital->where('hospital_code', $code)->first();
    }

}
