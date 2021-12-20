<?php

namespace Modules\RolePermission\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\RolePermission\Entities\Role;
use Modules\RolePermission\Entities\Permission;
use Auth;


class RoleRepository
{
    public function all()
    {
        return Role::orderBy('type')->get();
    }

    public function create(array $data)
    {
        $role = new Role();
        $role->name = $data['name'];
        $role->type = 'staff';
        $role->save();
    }

    public function update(array $data, $id)
    {
        return Role::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        $role = Role::find($id);
        if(count($role->users) > 0){
            return 'not_possible';
        }
        $role->delete();
        return 'possible';
    }

    public function normalRoles()
    {
        return Role::where('type','!=','admin')->get();
    }
}
