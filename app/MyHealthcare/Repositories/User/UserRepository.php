<?php

namespace App\MyHealthcare\Repositories\User;

use App\Exceptions\PasswordUpdateException;
use App\MyHealthcare\Helpers\Asset;
use App\MyHealthcare\Helpers\GenerateCode;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{
    /**
     * @var User
     */
    private $user;

    private $password;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function find($id)
    {
        return $this->user->with('roles', 'doctor', 'customerCare', 'frontEndDesk')->findOrFail($id);
    }

    public function create($request)
    {
        $user = $this->user;

        $user->email = $request->get('email');

        //$user->username = $request->get('username');

        $user->mobile_no = $request->get('mobile_no');

        $user->is_verified = $request->get('is_verified');

        $user->is_active = $request->get('is_active');

        $this->setPassword($request);

        $request->request->set("password", $this->password);

        $user->password = Hash::make($request->get('password'));

        $user->save();

        $user->roles()->attach($request->get('roles'));

        return $user;
    }

    public function updatePassword($request)
    {
        if (Auth::check()) {
            $id = Auth::user()->id;
        }

        $user = $this->user->find($id);

        if (Hash::check($request->get('old_password'), $user->password)) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
            return $user;
        }

        throw(new PasswordUpdateException("Wrong Current Password"));
    }

    protected function setPassword($request)
    {
        if ($request->has('password')) {
            $this->password = $request->get('password');
            return;
        }
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $this->password = implode($pass); //turn the array into a string
    }

    public function updateUserRoles($id, $request)
    {
        $user = $this->user->with('roles')->find($id);

        $role_id = $user->roles[0]->id;

        $user->mobile_no = $request->get('mobile_no');

        $user->is_verified = $request->get('is_verified');

        $user->is_active = $request->get('is_active');

        if($request->has('password'))
        {
            $user->password = Hash::make($request->get('password'));
        }

        $user->save();

        if ($user->isAdmin() || $user->isSuperAdmin())
        {
            $roles = $request->has('roles') ? $request->roles : [2];
            $user->roles()->sync($roles);
        }
        return $user;
    }

    public function delete($id)
    {
        $user = $this->user->find($id);
        $user->deleted_at = date("Y-m-d H:i:s");
        $user->save();
    }

    public function getUserRoles($user)
    {
        $user_roles = [];

        foreach ($user->roles as $role) {
            $user_roles[] = $role->id;
        }

        return $user_roles;
    }

    public function getCount()
    {
        return $this->user->count();
    }

    public function getProfile()
    {
        return $this->user->with('roles')->find(Auth::id());
    }

    public function getProfilePicture()
    {
        $user = $this->user->with('roles')->find(Auth::id());

        $relation = config('constants.ROLE_PROFILE.'.$user->roles[0]->name.'.userRelation');

        $profile = $user->$relation;

        return $profile->profile_picture;
    }

    public function isSuperAdmin()
    {
        $user = $this->user->find(Auth::id());
        return $user->isSuperAdmin();
    }

    public function isDoctor()
    {
        $user = $this->user->find(Auth::id());
        return $user->isDoctor();
    }

}
