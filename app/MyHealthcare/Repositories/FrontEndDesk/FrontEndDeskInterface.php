<?php

namespace App\MyHealthcare\Repositories\FrontEndDesk;

interface FrontEndDeskInterface
{
    public function getAll($keyword = null);

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);

    public function getFrontEndUserRoleIds($id);

    public function getCount();

    public function updateProfile($request);
}
