<?php

namespace App\Repositories;

use Exception;
use App\Models\Role;
use App\Classes\Helper;
use Illuminate\Support\Str;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\DB;

class RoleRepository
{
    public function index($request)
    {
        $paginateSize = $request->input('paginate_size', null);
        $paginateSize = Helper::checkPaginateSize($request);
        $displayName  = $request->input('display_name', null);

        try {
            $roles = new Role();

            if ($displayName) {
                $roles = $roles->where('display_name', 'like', "%$displayName%");
            }

            $roles = $roles->orderBy('display_name', 'asc')->paginate($paginateSize);

            return $roles;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $role = new Role();

            $role->display_name = $request->display_name;
            $role->name         = Str::slug($request->display_name, '-');
            $role->description  = $request->description;
            $role->save();

            if ($request->permission_ids && count($request->permission_ids) > 0) {
                $role->syncPermissions($request->permission_ids);
            }

            DB::commit();

            return $role;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {

            $role = Role::with('permissions')->find($id);

            if (!$role) {
                throw new CustomException('Role not found');
            }

            return $role;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $role = Role::find($id);
            if (!$role) {
                throw new CustomException('Role not found');
            }

            $role->display_name = $request->display_name;
            $role->name         = Str::slug($request->display_name);
            $role->description  = $request->description;
            $role->save();

            if ($request->permission_ids && count($request->permission_ids) > 0) {
                $role->syncPermissions($request->permission_ids);
            }

            DB::commit();

            return $role;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $role = Role::find($id);
            if (!$role) {
                throw new CustomException('Role not found');
            }

            return $role->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
