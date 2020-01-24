<?php

namespace App\MyHealthcare\Repositories\CustomerCare;

use App\MyHealthcare\Helpers\Asset;
use App\MyHealthcare\Helpers\GenerateCode;
use App\MyHealthcare\Repositories\User\UserInterface;
use App\Models\CustomerCare;
use App\User;
use Illuminate\Support\Facades\Auth;

class CustomerCareRepository implements CustomerCareInterface
{
    /**
     * @var CustomerCare
     */
    private $customerCare;

    /**
     * @var GenerateCode
     */
    private $generateCode;

    /**
     * @var Asset
     */
    private $asset;

    /**
     * @var User
     */
    private $user;

    /**
     * CustomerCareRepository constructor.
     * @param CustomerCare $customerCare
     */
    public function __construct(
        CustomerCare $customerCare,
        GenerateCode $generateCode,
        Asset $asset,
        UserInterface $user
    ) {

        $this->customerCare = $customerCare;

        $this->generateCode = $generateCode;

        $this->asset = $asset;

        $this->user = $user;
    }

    public function getAll($keyword = null)
    {
        return $keyword ? $this->customerCare->where('customer_care_code', 'LIKE', '%'. $keyword .'%')
                            ->orWhere('first_name', 'LIKE', '%'. $keyword .'%')
                            ->orWhere('last_name', 'LIKE', '%'. $keyword .'%')
                            ->orWhereRaw("concat(first_name,' ',last_name) like '%".$keyword."%' ")

        ->orWhereHas('user', function ($query) use ($keyword) {
                                $query->where('email', 'LIKE', '%'.$keyword.'%');
                            })
                            ->orWhereHas('user', function ($query) use ($keyword) {
                                $query->where('mobile_no', 'LIKE', '%'.$keyword.'%');
                            })
                            ->orderBy('id', 'DESC')
                            ->paginate(10) :
            $this->customerCare->orderBy('updated_at', 'DESC')->paginate(10);
    }

    public function find($id)
    {
        return $this->customerCare->with('user', 'user.roles')->findOrFail($id);
    }

    public function create($request)
    {
        $customer_care = $this->customerCare;

        $customer_care->customer_care_code = $this->generateCode->generateCode(
            $customer_care,
            'customer_care_code',
            'CAREID'
        );

        if($request->hasFile('profile_picture')){

            $customer_care->profile_picture = $this->asset->storeAsset('customerCares', 'customerCares', $request->file('profile_picture'));
        }

        $this->buildObject($request, $customer_care);

        $customer_care->created_by = Auth::id();

        $user = $this->user->create($request);

        $customer_care->user()->associate($user);

        $customer_care->save();

        return $customer_care;
    }

    private function buildObject($request, $customer_care)
    {
        $customer_care->first_name = $request->get('first_name');

        $customer_care->last_name = $request->get('last_name');

        $customer_care->address = $request->get('address');

        $customer_care->country_id = $request->get('country_id');

        $customer_care->state_id = $request->get('state_id');

        $customer_care->city_id = $request->get('city_id');

        $customer_care->pin_code = $request->get('pin_code');

        $customer_care->phone_number = $request->get('phone_number');

        $customer_care->gender = $request->get('gender');

        $customer_care->dob = $request->has('dob') ? $request->get('dob') : null;

        $customer_care->updated_by = Auth::id();
    }

    public function update($id, $request)
    {
        $customer_care = $this->customerCare->find($id);

        $this->buildObject($request, $customer_care);

        if($request->hasFile('profile_picture')){

            $customer_care->profile_picture = $this->asset->storeAsset('customerCares', 'customerCares', $request->file('profile_picture'));
        }

       $user = $this->user->updateUserRoles($customer_care->user_id, $request);

        $customer_care->save();

        return $customer_care;
    }

    public function delete($id)
    {
        $customerCare = $this->customerCare->find($id);
        $this->user->delete($customerCare->user_id);
        $customerCare->delete();
    }

    public function getRoleIds($customer_care)
    {
        $customer_care_roles = [];

        foreach ($customer_care->user->roles as $role) {
            $customer_care_roles[] = $role->id;
        }

        return $customer_care_roles;
    }

    public function getCount()
    {
        return $this->customerCare->count();
    }

    public function updateProfile($request)
    {
        $customer_care = $this->customerCare->where('user_id', Auth::id())->first();

        $this->buildObject($request, $customer_care);

        if($request->hasFile('profile_picture')){

            $customer_care->profile_picture = $this->asset->storeAsset('customerCares', 'customerCares', $request->file('profile_picture'));
        }

        $customer_care->save();

        return $customer_care;
    }
}
