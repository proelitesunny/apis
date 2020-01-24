<?php

namespace App\MyHealthcare\Repositories\InternationalPatientEstimate;

use App\Models\InternationalPatientEstimate;

class InternationalPatientEstimateRepository implements InternationalPatientEstimateInterface
{
	/**
	 * @var InternationalPatientEstimate
	 */
	private $internationalPatientEstimate;

	/**
     * @var Asset
     */
    private $asset;

	/**
	 * InternationalPatientEstimateRepository constructor.
	 * @param InternationalPatientEstimate $internationalPatientEstimate
	 */
	public function __construct(InternationalPatientEstimate $internationalPatientEstimate) {
		$this->internationalPatientEstimate = $internationalPatientEstimate;
	}

	public function create($params)
    {
    	//dd($params->get('prescription_pdf'));
        $internationalPatientEstimate = $this->internationalPatientEstimate;

        $internationalPatientEstimate->name = $params['name'];

        $internationalPatientEstimate->age = $params['age'];

        $internationalPatientEstimate->gender = $params['gender'];

        $internationalPatientEstimate->email = $params['email'];

        $internationalPatientEstimate->country_id = $params['country_id'];

        $internationalPatientEstimate->phone_number = $params['phone_number'];
        
        $internationalPatientEstimate->comments = $params['comments'];
        
        $internationalPatientEstimate->medical_procedure = $params['medical_procedure'];

        $internationalPatientEstimate->save();

        return $internationalPatientEstimate;
    }
}
