<?php

namespace App\MyHealthcare\Repositories\InternationalProcedureSurgery;

interface InternationalProcedureSurgeryInterface
{
	public function getAll($keyword = null);
	
	public function find($id);
    
    public function create($params);

    public function update($id, $params);

    public function getCount();
    
    public function delete($id);
    
    public function getList();
}
