<?php

namespace App\Http\Controllers\Api\Aggregator;

use Illuminate\Http\Request;
use App\Http\Requests\Api\Aggregator\Patients\CreatePatientRequest;
use App\Http\Requests\Api\Aggregator\Patients\UpdateRequest;

use Illuminate\Support\Facades\Log;
use App\MyHealthcare\Repositories\Patient\PatientInterface;

use App\MyHealthcare\Auth\JwtAuthentication;

class PatientsController extends BaseController
{   
    protected $patient;

    public function __construct(
        PatientInterface $patient
    )
    {   
        $this->patient = $patient;
    }

    public function create(CreatePatientRequest $request)
    {
        try {
            $patient = $this->patient->create($request);
        } catch( \App\Exceptions\PatientCreateException $e) {
            abort(400, $e->getMessage(), es($e->getErrorSource()));
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            abort(400, trans('errors.PATIENT_132'));
        }

        return response()->json([
                'message' => trans('success.PATIENT_103'),
                'patient_id' => $patient->id,
                    ], 200);
    }

    public function update(UpdateRequest $request)
    {
        $id = $request->patient_id;

        try {
            $patient = $this->patient->update($id, $request);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            abort(400, trans('errors.PATIENT_133'));
        }

        return response()->json([
                'message' => trans('success.PATIENT_102')
                    ], 200);
    }
}