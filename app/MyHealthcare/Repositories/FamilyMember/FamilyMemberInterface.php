<?php

namespace App\MyHealthcare\Repositories\FamilyMember;

interface FamilyMemberInterface
{
    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);
}
