<?php

namespace App\MyHealthcare\Repositories\Insurance;

interface InsuranceInterface
{
    public function create($request);

    public function update($id, $request);

    public function find($id);

    public function delete($id);
}
