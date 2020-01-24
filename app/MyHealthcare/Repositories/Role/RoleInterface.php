<?php

namespace App\MyHealthcare\Repositories\Role;

interface RoleInterface
{
    public function getAll($keyword = null);

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);

    public function getRolePermissionIds($id);

    public function getList();

    public function getCount();
}
