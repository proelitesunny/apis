<?php

namespace App\MyHealthcare\Repositories\Page;

interface PageInterface
{
	public function getAll($keyword = null);
	
	public function find($id);

    public function update($id, $params);
    
    public function create($params);
    
    public function getList();

    public function getCount();
    
    public function delete($id);

    public function getPage($keyword);
}
