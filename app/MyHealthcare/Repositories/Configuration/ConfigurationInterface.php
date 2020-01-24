<?php

namespace App\MyHealthcare\Repositories\Configuration;

interface ConfigurationInterface
{
    public function create($request);

    public function find($id);

    public function update($id, $request);

	public function getAll($keyword = null);

    public function delete($id);

    public function getConfiguration($keyword);
}
