<?php
/**
 * Created by PhpStorm.
 * User: a
 * Date: 26/5/17
 * Time: 5:05 PM
 */

namespace App\MyHealthcare\Repositories\State;

use App\Models\State;

class StateRepository implements StateInterface
{
    private $state;

    public function __construct(State $state)
    {
        $this->state = $state;
    }

    public function getStates($country_id)
    {
        return $this->state->where('country_id', $country_id)->pluck('name', 'id');
    }

    public function getStateByName($value)
    {
        return $this->state->where('name', strtolower($value))->first();
    }
}
