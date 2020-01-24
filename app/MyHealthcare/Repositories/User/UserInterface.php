<?php

namespace App\MyHealthcare\Repositories\User;

interface UserInterface
{
    public function find($id);

    public function create($params);

    public function updatePassword($request);

    public function updateUserRoles($id, $request);

    public function delete($id);

    public function getUserRoles($id);

    public function getCount();

    public function getProfile();

    public function getProfilePicture();

    public function isSuperAdmin();

    public function isDoctor();
}
