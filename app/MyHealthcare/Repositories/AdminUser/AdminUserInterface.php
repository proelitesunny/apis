<?php
namespace App\MyHealthcare\Repositories\AdminUser;

interface AdminUserInterface
{
    public function getAll($keyword = null);

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);

    public function getCount();

    public function getRoleIds($id);

    public function updateProfile($request);
}
