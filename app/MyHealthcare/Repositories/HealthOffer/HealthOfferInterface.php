<?php

namespace App\MyHealthcare\Repositories\HealthOffer;

interface HealthOfferInterface
{
    public function create($params, $authorId);

    public function find($id);

    public function getHealthOffers();

    public function update($id, $params, $authorId);

    public function delete($id, $authorId);

    public function getCount();

    public function getAll($keyword = null);
}
