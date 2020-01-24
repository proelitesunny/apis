<?php

namespace App\MyHealthcare\Repositories\AuditLogger;

interface AuditInterface
{
    /**
     * @param $data
     * @param $author
     * @return mixed
     */
    public function create($data, $author);

    /**
     * @return mixed
     */
    public function getAll($keyword = null);

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);
}