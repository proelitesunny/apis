<?php

namespace App\MyHealthcare\Repositories\InsuranceTieUp;

interface InsuranceTieUpInterface
{
	public function getAll($keyword = null);

	public function create($params);

	public function find($id);

	public function update($id, $params);

	public function getCount();

	public function delete($id);
}
