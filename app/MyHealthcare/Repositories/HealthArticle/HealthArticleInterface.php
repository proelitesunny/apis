<?php

namespace App\MyHealthcare\Repositories\HealthArticle;

interface HealthArticleInterface
{
    public function getAll($keyword = null);

    public function create($params);
    
    public function checkUniqueArticleName($params);

    public function find($id);

    public function update($id, $params);

    public function getCount();

    public function delete($id);
    
}

