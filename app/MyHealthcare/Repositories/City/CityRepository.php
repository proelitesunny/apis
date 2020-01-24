<?php

namespace App\MyHealthcare\Repositories\City;

use App\Models\City;

class CityRepository implements CityInterface
{
    /**
     * @var City
     */
    private $city;

    /**
     * CityRepository constructor.
     * @param City $city
     */
    public function __construct(City $city)
    {
        $this->city = $city;
    }

    /**
     * @param $stateId
     * @return mixed
     */
    public function getCitiesByStateId($stateId)
    {
        return $this->city->where('state_id', $stateId)->pluck('name', 'id');
    }

    public function getCityByName($value)
    {
        return $this->city->where('name', strtolower($value))->first();
    }
}
