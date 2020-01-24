<?php

namespace App\MyHealthcare\Repositories\AuditLogger;

use App\Models\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditRepository implements AuditInterface
{
    /**
     * @var AuditLogger
     */
    private $auditLogger;

    /**
     * AuditRepository constructor.
     * @param AuditLogger $auditLogger
     */
    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /**
     * @param $data
     * @param $author
     * @return AuditLogger
     */
    public function create($data, $author)
    {
        $auditLogger = $this->auditLogger;

        $auditLogger->model = $data['model'];
        $auditLogger->data = $data['logData'];
        $auditLogger->action_taken = $data['actionTaken'];
        $auditLogger->user_id = $author;

        $auditLogger->save();

        return $auditLogger;
    }

    /**
     * @param null $keyword
     * @return mixed
     */
    public function getAll($keyword = null)
    {
        return $keyword ? $this->auditLogger->with('user')
            ->where('model', 'LIKE', '%' . $keyword . '%')
            ->orWhere('action_taken', 'LIKE', '%' . $keyword . '%')
            ->orderBy('created_at', 'DESC')->paginate(10) :

            $this->auditLogger->with('user')->orderBy('created_at', 'DESC')->paginate(10);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->auditLogger->with('user')->find($id);
    }
}