<?php


namespace App\MyHealthcare\Repositories\State;

interface StateInterface
{
    public function getStates($country_id);

    public function getStateByName($value);
}
