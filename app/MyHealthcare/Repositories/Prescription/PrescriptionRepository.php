<?php

namespace App\MyHealthcare\Repositories\Prescription;

use App\MyHealthcare\Helpers\Asset;
use App\MyHealthcare\Helpers\GenerateCode;
use App\Http\Requests\Prescription\CreatePrescriptionRequest;
use App\Models\Prescription;

class PrescriptionRepository implements PrescriptionInterface
{
	/**
	 * @var Prescription
	 */
	private $prescription;

    /**
     * @var GenerateCode
     */
	private $generateCode;

    /**
     * @var Asset
     */
	private $asset;

    /**
     * PrescriptionRepository constructor.
     * @param Prescription $prescription
     * @param GenerateCode $generateCode
     * @param Asset $asset
     */
	public function __construct(Prescription $prescription, GenerateCode $generateCode, Asset $asset) {
		$this->prescription = $prescription;

		$this->generateCode = $generateCode;

		$this->asset = $asset;
	}

    public function getAll($keyword = null)
    {
        return $keyword ? $this->prescription->with('patient','doctor', 'facility')
                            ->where('prescription_code', 'LIKE', '%'. $keyword. '%')
                            ->orWhereHas('facility', function ($query) use ($keyword) {
                                $query->where('name', 'LIKE', '%'.$keyword.'%');
                            })
                            ->orWhereHas('patient', function ($query) use ($keyword){
                                $query->where('first_name', 'LIKE', '%' .$keyword. '%');
                            })
                            ->paginate(10)
                : $this->prescription->with('patient', 'doctor', 'facility')->paginate(10);
    }

    public function find($id)
    {
        return $this->prescription->with('patient', 'facility', 'doctor')->find($id);
    }

    public function create($request)
    {
       $prescription = $this->prescription;

        $prescription->prescription_code = $this->generateCode->generateCode($prescription, 'prescription_code', 'PRESCRIPT');

        $prescription->upload_invoice = $request->hasFile('upload_invoice') ?
            $this->asset->storeAsset('assets', 'prescription', $request->file('$prescription')) :
            null;

        $prescription->upload_prescription = $request->hasFile('upload_prescription') ?
            $this->asset->storeAsset('assets', 'prescription', $request->file('$prescription')) :
            null;

        $prescription->patient_id = $request->get('patient_id');

        $prescription->follow_up_days = $request->has('follow_up_days') ? $request->get('follow_up_days') : null;

        $this->buildObject($request, $prescription);

        $prescription->save();

        return $prescription;
    }

    /**
     * @param $request CreatePrescriptionRequest
     * @param $prescription
     */
    public function buildObject($request, $prescription)
    {
        $prescription->doctor_id = $request->has('doctor_id') ? $request->get('doctor_id') : null;
//        $prescription->patient_id = $request->get('patient_id');
        $prescription->facility_id = $request->get('facility_id');
        $prescription->prescription_date = $request->has('prescription_date') ? $request->get('prescription_date') : null;
        $prescription->complaint = $request->get('complaint');
        $prescription->follow_up = $request->get('follow_up');
//        $prescription->follow_up_days = $request->has('follow_up_days') ? $request->get('follow_up_days') : null;
        $prescription->symptoms = $request->has('symptoms') ? $request->get('symptoms') : null;
        $prescription->investigation = $request->has('investigation') ? $request->get('investigation') : null;
        $prescription->advice = $request->has('advice') ? $request->get('advice') : null;
        $prescription->referred_by = $request->has('referred_by') ? $request->get('referred_by') : null;
        $prescription->referred_to = $request->has('referred_to') ? $request->get('referred_to') : null;
        $prescription->referral_comment = $request->has('referral_comment') ? $request->get('referral_comment') : null;
        $prescription->note = $request->has('note') ? $request->get('note') : null;
    }

    public function update($id, $request)
    {
        $prescription = $this->find($id);

        $prescription->upload_invoice = $request->hasFile('upload_invoice') ?
            $this->asset->storeAsset('assets', 'prescription', $request->file('upload_invoice')) :
            $prescription->upload_invoice;

        $prescription->upload_prescription = $request->hasFile('upload_prescription') ?
            $this->asset->storeAsset('assets', 'prescription', $request->file('upload_prescription')) :
            $prescription->upload_prescription;

        $prescription->follow_up_days = ($request->get('follow_up') == 1) ? $request->get('follow_up_days') : null;


        $this->buildObject($request, $prescription);

        $prescription->save();

        return $prescription;
    }

    public function delete($id)
    {
        $prescription = $this->find($id);

        $prescription->delete();

//        return true;
    }


}
