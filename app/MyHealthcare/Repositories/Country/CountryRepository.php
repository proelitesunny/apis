<?php

namespace App\MyHealthcare\Repositories\Country;

use App\Models\Country;

class CountryRepository implements CountryInterface
{
    /**
     * @var Country
     */
    private $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function getAll()
    {
        return $this->country->pluck('name', 'id')->prepend('Select Country','');
    }

    public function getAllWithCode()
    {
        return $this->country->pluck('name', 'code')->prepend('Select Country','');
    }
    
    public function getAllDump()
    {
        return $this->country->select('id', 'name', 'isd_code')->orderBy('name', 'asc')->get();
    }

    public function getCountryByName($value)
    {
        return $this->country->where('name', strtolower($value))->first();
    }
    
}