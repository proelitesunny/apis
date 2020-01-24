<?php

namespace App\MyHealthcare\Repositories\FamilyMember;

use App\MyHealthcare\Helpers\GenerateCode;
use App\MyHealthcare\Repositories\Patient\PatientInterface;
use App\Models\FamilyMember;

class FamilyMemberRepository implements FamilyMemberInterface
{
	/**
	 * @var FamilyMember
	 */
	private $familyMember;

    /**
     * @var GenerateCode
     */
	private $generateCode;

    /**
     * FamilyMemberRepository constructor.
     * @param FamilyMember $familyMember
     * @param GenerateCode $generateCode
     * @param PatientInterface $patient
     */
	public function __construct(FamilyMember $familyMember, GenerateCode $generateCode) {
		$this->familyMember = $familyMember;

		$this->generateCode = $generateCode;
	}

    public function find($id)
    {
        return $this->familyMember->with('patient')->findOrFail($id);
    }

    public function create($request)
    {
        $familyMember = $this->familyMember;

        $familyMember->family_member_code = $this->generateCode->generateCode($familyMember, 'family_member_code', 'MEMID');

        $this->buildObject($request, $familyMember);

        $familyMember->save();

        return $familyMember;
    }

    public function buildObject($request, $familyMember)
    {
        $familyMember->patient_id = $request->get('patient_id');

        $familyMember->first_name = $request->get('first_name');

        $familyMember->last_name = $request->get('last_name');

        $familyMember->email = $request->get('email');

        $familyMember->mobile_no = $request->get('mobile_no');

        $familyMember->relationship = $request->get('relationship');

    }

    public function update($id, $request)
    {
        $familyMember = $this->familyMember->find($id);

        $this->buildObject($request, $familyMember);

        $familyMember->save();

        return $familyMember;
    }

    public function delete($id)
    {
        $familyMember = $this->familyMember->find($id);

        $familyMember->delete();

        return true;
    }


}