<?php

namespace App\MyHealthcare\Repositories\Faq;

interface FaqInterface
{
	public function getAll($keyword = null);

	public function create($params);
	
	public function find($id);

    public function update($id, $params);

    public function getCount();

    public function delete($id);
    
	/*
    
    public function getList();
    


    */
}
