<?php

namespace App\MyHealthcare\Repositories\MasterPatientIndex;

use App\MyHealthcare\Helpers\GenerateCode;
use App\Models\MasterPatientIndex;
use Illuminate\Support\Facades\Hash;

class MasterPatientIndexRepository implements MasterPatientIndexInterface
{
	/**
	 * @var MasterPatientIndex
	 */
	private $masterPatientIndex;

	private $generateCode;

	/**
	 * MasterPatientIndexRepository constructor.
	 * @param MasterPatientIndex $masterPatientIndex
	 */
	public function __construct(MasterPatientIndex $masterPatientIndex, GenerateCode $generateCode) {
		$this->masterPatientIndex = $masterPatientIndex;

		$this->generateCode = $generateCode;
	}

    public function find($request)
    {
        $is_registered = $this->masterPatientIndex->where('mobile_no', $request->mobile_no)->first();

        if(!empty($is_registered))
        {
            return true;
        }
        return false;
        //throw new \Exception('mobile number already registered',100001);
    }

    public function create($request)
    {
       $masterPatientIndex = $this->masterPatientIndex;

        // $code_prefix = config('constants.CODE_PREFIX.MasterPatient');

        // $masterPatientIndex->patient_code = $this->generateCode->generateCode(
        //    $masterPatientIndex,
        //    'patient_code',
        //     $code_prefix );

       $masterPatientIndex->mobile_no = $request->mobile_no;

       $masterPatientIndex->password = Hash::make('admin123');

       if ($request->has('is_verified')) {
            //dd($request->all());
            $masterPatientIndex->is_verified = $request->is_verified;
        }

       $masterPatientIndex->save();

       return $masterPatientIndex;
    }

    public function update($masterId, $request)
    {
        $masterDetails = $this->masterPatientIndex->findOrFail($masterId);

        if ($request->has('is_verified')) {
            $masterDetails->is_verified = $request->is_verified;
        }

        $masterDetails->save();

        return $masterDetails;
    }

    public function isPatientExist($user)
    {
        try {
            return $this->masterPatientIndex->where('mobile_no', $user)->firstOrFail();
        }
        catch(\Exception $e) {
            return false;
        }
    }

    public function validateLogin($request)
    {
        $masterPatientIndex = null;
        $password = null;

        try {
            $masterPatientIndex = $this->isPatientExist($request->input('uid'));
            $password = $masterPatientIndex->password;
            // $patient = $this->patient->with('patientHealthDetails')->where('mobile_no', $request->input('uid'))->orWhere('patient_code', $request->input('uid'))->firstOrFail();
        }
        catch(\Exception $e) {
            abort(404, trans('errors.PATIENT_108'), es('uid'));
        }

        // Check if Password matched
        if (!Hash::check($request->input('password'), $password)) {
            abort(400, trans('errors.PATIENT_105'), es('password'));
        }

        // Update is_logged_in flag
        // $patient->is_logged_in = 1;
        // $patient->save();

        return $masterPatientIndex;
    }

    public function setOtp($request)
    {
        $otp = random_int(1000, 9999);
        $otp_expired_at = date('Y-m-d H:i:s', strtotime('+' . config('api.aggregator_api.login.otp_expiration') . ' minutes'));

        if (!$masterPatientIndex = $this->isPatientExist($request->input('uid'))) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(404, trans('errors.PATIENT_108'), null, es('uid'));
        }

        // Set OTP to null
        $masterPatientIndex->otp = $otp;
        $masterPatientIndex->otp_expired_at = $otp_expired_at;
        $masterPatientIndex->save();

        // Log OTP Details
        dispatch(new \App\Jobs\SendSmsNotification($masterPatientIndex->mobile_no, "OTP Details sent on ".$masterPatientIndex->mobile_no."\n " . sprintf("OTP %s will expire after, %s", $otp, (string) $otp_expired_at)));

        return $masterPatientIndex;
    }

    public function validateOtp($request)
    {
        $masterPatientIndex = null;
        $otp = null;
        $otp_expired_at = null;

        $now = \Carbon\Carbon::now();

        try {
            $masterPatientIndex = $this->isPatientExist($request->input('uid'));
            $otp = $masterPatientIndex->otp;
            $otp_expired_at = \Carbon\Carbon::parse($masterPatientIndex->otp_expired_at);
            // $patient = $this->patient->with('patientHealthDetails')->where('mobile_no', $request->input('uid'))->orWhere('patient_code', $request->input('uid'))->firstOrFail();
        }
        catch(\Exception $e) {
            abort(400, trans('errors.PATIENT_108'), es('uid'));
        }

        // Check if OTP matched within expire time
        if ($request->input('otp') != $otp || $now->gt($otp_expired_at)) {
            abort(400, trans('errors.PATIENT_131'), es('otp'));
        }

        // Set OTP to null
        $masterPatientIndex->otp = null;
        $masterPatientIndex->otp_expired_at = null;
        $masterPatientIndex->is_verified = 1;

        // Update is_logged_in flag
        // $patient->is_logged_in = 1;

        $masterPatientIndex->save();

        return $masterPatientIndex;
    }

    public function setPassword($request, $id)
    {
        try {
            $masterPatientIndex = $this->isPatientExist($id);
            $masterPatientIndex->password = bcrypt($request->input('password'));
            $masterPatientIndex->save();
            return $masterPatientIndex;
        }
        catch(\Exception $e) {
            abort(400, trans('errors.PATIENT_129'));
        }
    }



}
