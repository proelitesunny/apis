<?php

namespace App\MyHealthcare\Repositories\HospitalHoliday;

use App\Models\HospitalHoliday;
use Illuminate\Support\Facades\Auth;

class HospitalHolidayRepository implements HospitalHolidayInterface
{
	/**
	 * @var HospitalHoliday
	 */
	private $hospitalHoliday;

	/**
	 * HospitalHolidayRepository constructor.
	 * @param HospitalHoliday $hospitalHoliday
	 */
	public function __construct(HospitalHoliday $hospitalHoliday) {
		$this->hospitalHoliday = $hospitalHoliday;
	}

	public function getAll($keyword = null)
    {
        return $keyword ?

        $this->hospitalHoliday->with('hospitals')
                ->where(function($query) use($keyword) {
                	$query->where('reason', 'LIKE', '%'.$keyword.'%');
                	$query->orWhere('date', 'LIKE', '%'.$keyword.'%');
                    
                    })->orWhereHas('hospitals', function ($query) use ($keyword) {
                                $query->where('name', 'LIKE', '%' . $keyword . '%');
                            })
			->orderBy('updated_by','DESC')
            ->paginate(10)
        :
        $this->hospitalHoliday->orderBy('updated_by', 'DESC')->paginate(10);
    }

	public function create($request)
    {
        $hospitalHoliday = $this->hospitalHoliday;

        $this->buildObject($request, $hospitalHoliday);

        $hospitalHoliday->created_by = Auth::id();

	    $hospitalHoliday->save();

	    $hospitalHoliday->hospitals()->attach($request->get('hospital_id'));

        return $hospitalHoliday;
    }

    public function find($id)
    {
        return $this->hospitalHoliday->with('hospitals')->findOrFail($id);
    }

    public function update($id, $request)
    {

        $hospitalHoliday = $this->hospitalHoliday->find($id);

        $this->buildObject($request, $hospitalHoliday);

        $hospitalHoliday->save();

        $hospitalHoliday->hospitals()->sync($request->get('hospital_id'));

        return $hospitalHoliday;
    }

    public function delete($id)
    {
        $hospitalHoliday = $this->hospitalHoliday->find($id);
        $hospitalHoliday->delete();
        $hospitalHoliday->hospitals()->sync([]);
    }

    private function buildObject($request, $hospitalHoliday)
    {

        $hospitalHoliday->date = $request->get('date');

        $hospitalHoliday->reason = $request->get('reason');

        $hospitalHoliday->updated_by = Auth::id();
    }


    public function getHospitalIds($hospitalHoliday)
    {
        $hospitalIds = [];

        foreach ($hospitalHoliday->hospitals as $hospital) {
            $hospitalIds[] = $hospital->id;
        }

        return $hospitalIds;
    }
}
