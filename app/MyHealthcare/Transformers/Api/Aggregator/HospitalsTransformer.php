<?php

namespace App\MyHealthcare\Transformers\Api\Aggregator;

use App\MyHealthcare\Transformers\Api\BaseTransformer;

class HospitalsTransformer extends BaseTransformer
{
    public function getHospitalData($hospital)
	{
		return [
			'id' => $hospital->id,
			'name' => $hospital->name,
			'woodlands_hospital_code' => $hospital->woodlands_hospital_code,
			'emergency_contact' => $hospital->emergency_contact,
			'lat'=> $hospital->latitude,
			'lng' => $hospital->longitude,
                        'address' => $hospital->address,
                        'actions' => [
                                        [
                                            'name' => 'View',
                                            'type' => config('api.types.PATIENTS_130'),
                                            'uri' => 'hospitals/' . $hospital->id
                                        ]
                                    ]
		];
	}
}
