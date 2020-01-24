<?php

namespace App\MyHealthcare\Repositories\InternationalProcedureSurgery;

use App\Models\InternationalProcedureSurgery;

class InternationalProcedureSurgeryRepository implements InternationalProcedureSurgeryInterface
{
	/**
	 * @var InternationalProcedureSurgery
	 */
	private $internationalProcedureSurgery;

	/**
	 * InternationalProcedureSurgeryRepository constructor.
	 * @param InternationalProcedureSurgery $internationalProcedureSurgery
	 */
	public function __construct(InternationalProcedureSurgery $internationalProcedureSurgery) {
		$this->internationalProcedureSurgery = $internationalProcedureSurgery;
	}

	public function getAll($keyword = null)
    {
        if (!$keyword) {
            return $this->internationalProcedureSurgery->orderBy('id',"desc")->paginate(10);
        }
        return $this->internationalProcedureSurgery->where('title', 'LIKE', '%'.$keyword.'%')
            ->orWhere('description', 'LIKE', '%'.$keyword.'%')
            ->orWhere('real_cost_estimates', 'LIKE', '%'.$keyword.'%')
            ->paginate(10);
    }

	public function find($id)
    {
        return $this->internationalProcedureSurgery->find($id);
    }

    public function create($params)
    {
        $internationalProcedureSurgery = $this->internationalProcedureSurgery;

        $internationalProcedureSurgery->title = $params['title'];

        $internationalProcedureSurgery->description = $params['description'];
        
        $internationalProcedureSurgery->real_cost_estimates = $params['hdn_cost_array'];

        $internationalProcedureSurgery->save();

        return $internationalProcedureSurgery;
    }

    public function update($id, $params)
    {

        $internationalProcedureSurgery = $this->internationalProcedureSurgery->find($id);

        $internationalProcedureSurgery->title = $params['title'];

        $internationalProcedureSurgery->description = $params['description'];
        
        $internationalProcedureSurgery->real_cost_estimates = $params['hdn_cost_array'];

        $internationalProcedureSurgery->save();
        
        return $internationalProcedureSurgery;
    }

    public function getCount()
    {
        return $this->internationalProcedureSurgery->count();
    }

    public function delete($id)
    {
        $internationalProcedureSurgery = $this->internationalProcedureSurgery->find($id);
        $internationalProcedureSurgery->delete();
    }

    public function getList()
    {
        return $this->internationalProcedureSurgery->get();
    }

}
