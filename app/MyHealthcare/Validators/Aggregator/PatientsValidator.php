<?php 

namespace App\MyHealthcare\Validators\Api\Aggregator;

use App\MyHealthcare\Validators\Api\BaseValidator;
use Illuminate\Support\Facades\Validator;

use App\MyHealthcare\Repositories\Patient\PatientInterface;

class PatientsValidator extends BaseValidator
{
	protected $patient;

	public function __construct(PatientInterface $patient)
	{
		$this->patient = $patient;
	}

	public function isVerified()
	{
		Validator::extend('is_verified', function ($attribute, $value, $parameters, $validator) {
			return $this->patient->isVerified($value);
		});
	}
}