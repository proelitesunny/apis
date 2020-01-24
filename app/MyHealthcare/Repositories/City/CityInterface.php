<?php

namespace App\MyHealthcare\Repositories\City;

interface CityInterface
{
    public function getCitiesByStateId($stateId);

    public function getCityByName($value);
}
