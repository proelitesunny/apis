<?php

namespace App\MyHealthcare\Repositories\FrontEndDesk;

use App\MyHealthcare\Helpers\Asset;
use App\MyHealthcare\Helpers\GenerateCode;
use App\MyHealthcare\Repositories\User\UserInterface;
use App\Models\FrontEndDesk;
use App\User;
use Illuminate\Support\Facades\Auth;

class FrontEndDeskRepository implements FrontEndDeskInterface
{
    /**
     * @var FrontEndDesk
     */
    private $frontEndDesk;

    /**
     * @var GenerateCode
     */
    private $generateCode;

    private $user;

    private $asset;

    /**
     * FrontEndDeskRepository constructor.
     * @param FrontEndDesk $frontEndDesk
     */
    public function __construct(
        FrontEndDesk $frontEndDesk,
        GenerateCode $generateCode,
        Asset $asset,
        UserInterface $user
    ) {
        $this->frontEndDesk = $frontEndDesk;

        $this->generateCode = $generateCode;

        $this->asset = $asset;

        $this->user = $user;
    }

    public function getAll($keyword = null)
    {
        return $keyword ? $this->frontEndDesk->with('user')
            ->where('front_end_desk_code', 'LIKE', '%'. $keyword .'%')
            ->orWhere('first_name', 'LIKE', '%'. $keyword .'%')
            ->orWhere('last_name', 'LIKE', '%'. $keyword .'%')
            ->orWhereRaw("concat(first_name,' ',last_name) like '%".$keyword."%' ")
            ->orWhereHas('user', function ($query) use ($keyword) {
                $query->where('email', 'LIKE', '%'.$keyword.'%');
            })
            ->orWhereHas('user', function ($query) use ($keyword) {
                $query->where('mobile_no', 'LIKE', '%'.$keyword.'%');
            })
            ->orWhereHas('hospital', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%'.$keyword.'%');
            })
            ->orderBy('id','DESC')
            ->paginate(10) :
            $this->frontEndDesk->orderBy('updated_by','DESC')->paginate(10);
    }

    public function find($id)
    {
        return $this->frontEndDesk->with('user', 'hospital')->findOrFail($id);
    }

    public function create($request)
    {
        $front_end_desk = $this->frontEndDesk;

        $front_end_desk->front_end_desk_code = $this->generateCode->generateCode(
            $front_end_desk,
            'front_end_desk_code',
            'DESKID'
        );

        $front_end_desk->profile_picture = $request->hasFile('profile_picture') ?
            $this->asset->storeAsset('frontEndDesks', 'frontEndDesks', $request->file('profile_picture')) :
            null;

        $this->buildObject($request, $front_end_desk);

        $front_end_desk->created_by = Auth::id();

        $user = $this->user->create($request);

        $front_end_desk->user()->associate($user);

        $front_end_desk->save();

        return $front_end_desk;
    }

    private function buildObject($request, $front_end_desk)
    {
        $front_end_desk->first_name = $request->get('first_name');

        $front_end_desk->last_name = $request->get('last_name');

        $front_end_desk->address = $request->get('address');

        $front_end_desk->country_id = $request->get('country_id');

        $front_end_desk->state_id = $request->get('state_id');

        $front_end_desk->city_id = $request->get('city_id');

        $front_end_desk->pin_code = $request->get('pin_code');

        $front_end_desk->hospital_id = $request->get('hospital_id');

        $front_end_desk->phone_number = $request->get('phone_number');

        $front_end_desk->dob = $request->has('dob') ? $request->get('dob') : null;

        $front_end_desk->gender = $request->get('gender');

        $front_end_desk->updated_by = Auth::id();
    }

    public function update($id, $request)
    {
        $front_end_desk = $this->frontEndDesk->find($id);

        $this->buildObject($request, $front_end_desk);

        if($request->hasFile('profile_picture')){

            $front_end_desk->profile_picture = $this->asset->storeAsset('frontEndDesks', 'frontEndDesks', $request->file('profile_picture'));
        }

        $user = $this->user->updateUserRoles($front_end_desk->user_id, $request);

        $front_end_desk->save();

        return $front_end_desk;
    }

    public function delete($id)
    {
        $front_end_desk = $this->frontEndDesk->find($id);
        $this->user->delete($front_end_desk->user_id);
        $front_end_desk->delete();
    }

    public function getFrontEndUserRoleIds($front_end_desk)
    {
        $front_end_desk_roles = [];

        foreach ($front_end_desk->user->roles as $role) {
            $front_end_desk_roles[] = $role->id;
        }

        return $front_end_desk_roles;
    }

    public function getCount()
    {
        return $this->frontEndDesk->count();
    }

    public function updateProfile($request)
    {
        $front_end_desk = $this->frontEndDesk->where('user_id', Auth::id())->first();

        $this->buildObject($request, $front_end_desk);

        if($request->hasFile('profile_picture')){

            $front_end_desk->profile_picture = $this->asset->storeAsset('frontEndDesks', 'frontEndDesks', $request->file('profile_picture'));
        }

        $front_end_desk->save();

        return $front_end_desk;
    }
}
