<?php

namespace App\MyHealthcare\Repositories\Permission;

interface PermissionInterface
{
    public function getAll($keyword = null);

    public function find($id);

    public function create($request);

    public function update($id, $params);

    public function delete($id);

    public function getList();

    public function getCount();
}
