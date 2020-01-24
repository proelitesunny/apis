<?php

namespace App\MyHealthcare\Repositories\Country;

interface CountryInterface
{
    public function getAll();

    public function getAllWithCode();
    
    public function getAllDump();

    public function getCountryByName($value);
}