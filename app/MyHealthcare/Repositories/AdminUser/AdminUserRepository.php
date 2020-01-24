<?php

namespace App\MyHealthcare\Repositories\AdminUser;

use App\MyHealthcare\Helpers\Asset;
use App\MyHealthcare\Helpers\GenerateCode;
use App\MyHealthcare\Repositories\User\UserInterface;
use App\Models\AdminUser;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminUserRepository implements AdminUserInterface
{
	/**
	 * @var AdminUser
	 */
	private $adminUser;

	private $generateCode;

	private $asset;

	private $user;

	/**
	 * AdminUserRepository constructor.
	 * @param AdminUser $adminUser
	 */
	public function __construct(
	    AdminUser $adminUser,
        GenerateCode $generateCode,
        Asset $asset,
        UserInterface $user) {

		$this->adminUser = $adminUser;

		$this->generateCode = $generateCode;

		$this->asset = $asset;

		$this->user = $user;
	}

    public function getAll($keyword = null)
    {
         return $keyword ? $this->adminUser->where('user_id','!=',Auth::getUser()->id)
                ->where( function($query) use ($keyword){
                    $query->whereHas('user.roles', function($query) use ($keyword){
                        if(!$this->user->isSuperAdmin()) {
                            $query->where('id', '!=', 1);
                        }
                    })->where( function ($query) use ($keyword){
                           $query->where('admin_user_code', 'LIKE', '%'. $keyword .'%');
                           $query->orWhere('first_name', 'LIKE', '%'. $keyword .'%');
                           $query->orWhere('last_name', 'LIKE', '%'. $keyword .'%');
                           $query->orWhereRaw("concat(first_name,' ',last_name) like '%".$keyword."%' ");


                    $query->orWhereHas('user', function ($query) use ($keyword) {

                               $query->where('email', 'LIKE', '%'.$keyword.'%');
                       });
                        $query->orWhereHas('user', function ($query) use ($keyword) {
                            $query->where('mobile_no', 'LIKE', '%'.$keyword.'%');
                        });
                   });
                })
                ->orderBy('id', 'DESC')
                ->paginate(10) :

                $this->adminUser->where('user_id','!=',Auth::getUser()->id)->whereHas('user.roles', function($query) {
                        if(!$this->user->isSuperAdmin())
                    $query->where('id', '!=', 1);
                })
                ->orderBy('updated_at', 'DESC')
                ->paginate(10);
    }

    public function find($id)
    {        
        return $this->adminUser->with('user', 'user.roles')->where('user_id','!=',Auth::getUser()->id)->findOrFail($id);
    }

    public function create($request)
    {
        $admin_user = $this->adminUser;

        $admin_user->admin_user_code = $this->generateCode->generateCode(
            $admin_user,
            'admin_user_code',
            'ADMINID'
        );

        $admin_user->profile_picture = $request->hasFile('profile_picture') ?
            $this->asset->storeAsset('adminUsers', 'adminUsers', $request->file('profile_picture')) :
            null;

        $this->buildObject($request, $admin_user);

        $admin_user->created_by = Auth::id();

        $user = $this->user->create($request);

        $admin_user->user()->associate($user);

        $admin_user->save();

        return $admin_user;
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $audit = $this->adminUser->newInstance()->find($id);

            $admin_user = $this->adminUser->newInstance()->find($id);

            $this->buildObject($request, $admin_user);

            if ($request->hasFile('profile_picture')) {
                $admin_user->profile_picture = $this->asset->storeAsset('adminUsers', 'adminUsers', $request->file('profile_picture'));
            }

            $user = $this->user->updateUserRoles($admin_user->user_id, $request);

            $admin_user->save();

            DB::commit();

            return $admin_user;

        } catch (QueryException $e) {
            DB::rollBack();
            logger($e->getMessage());
            throw(new \Exception('Database exception'));
        }
    }

    public function delete($id)
    {
        $admin_user = $this->adminUser->find($id);
        $this->user->delete($admin_user->user_id);
        $admin_user->delete();
    }

    public function getRoleIds($admin_user)
    {
        $admin_user_roles = [];

        foreach ($admin_user->user->roles as $role) {
            $admin_user_roles[] = $role->id;
        }

        return $admin_user_roles;
    }

    public function getCount()
    {
        return $this->adminUser->where('user_id','!=',Auth::getUser()->id)->count();
    }

    public function updateProfile($request)
    {
        $admin_user = $this->adminUser->find(Auth::id());

        $this->buildObject($request, $admin_user);

        if($request->hasFile('profile_picture')){

            $admin_user->profile_picture = $this->asset->storeAsset('adminUsers', 'adminUsers', $request->file('profile_picture'));
        }

        $admin_user->save();

        return $admin_user;
    }

    private function buildObject($request, $admin_user)
    {
        $admin_user->first_name = $request->get('first_name');

        $admin_user->last_name = $request->get('last_name');

        $admin_user->address = $request->get('address');

        $admin_user->country_id = $request->get('country_id');

        $admin_user->state_id = $request->get('state_id');

        $admin_user->city_id = $request->get('city_id');

        $admin_user->pin_code = $request->get('pin_code');

        $admin_user->phone_number = $request->get('phone_number');

        $admin_user->gender = $request->get('gender');
        
        $admin_user->dob = $request->has('dob') ? $request->get('dob') : null;

        $admin_user->updated_by = Auth::id();
    }
}
