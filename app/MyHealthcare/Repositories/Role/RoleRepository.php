<?php

namespace App\MyHealthcare\Repositories\Role;

use App\Role;
use Illuminate\Support\Facades\DB;

class RoleRepository implements RoleInterface
{
    /**
     * @var Role
     */
    private $role;

    /**
     * RoleRepository constructor.
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function getAll($keyword = null)
    {
        return $keyword ? $this->role->where('name', 'LIKE', '%'. $keyword . '%')
                            ->orWhere('display_name', 'LIKE', '%'. $keyword . '%')
                            ->orWhere('description', 'LIKE', '%'. $keyword .'%')
                            ->paginate(10) :

                 $this->role->paginate(10);
    }

    public function find($id)
    {
        return $this->role->with('permissions')->findOrFail($id);
    }

    public function create($request)
    {
        $role = $this->role;

        $role->name = $request->get('name');

        $role->display_name = $request->get('display_name');

        $role->description = $request->get('description');

        $role->save();

        $role->permissions()->attach($request->get('permissions'));

        return $role;
    }

    public function update($id, $request)
    {
        $role = $this->role->find($id);

        $role->name = $request->get('name');

        $role->display_name = $request->get('display_name');

        $role->description = $request->get('description');

        $role->save();

        $role->permissions()->sync($request->get('permissions'));

        return $role;
    }

    public function delete($id)
    {
        $role = $this->role->find($id);

        $role->delete();
    }

    public function getRolePermissionIds($role)
    {
        $rolePermissionIds = [];

        foreach ($role->permissions as $permission) {
            $rolePermissionIds[] = $permission->id;
        }

        return $rolePermissionIds;
    }

    public function getList()
    {
        return $this->role->pluck('display_name', 'id');
    }

    public function getCount()
    {
        return $this->role->count();
    }
}
