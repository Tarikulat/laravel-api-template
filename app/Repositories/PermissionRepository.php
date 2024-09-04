<?php

namespace App\Repositories;

use Exception;
use App\Models\Permission;
use App\Classes\Helper;
use Illuminate\Support\Str;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\DB;

class PermissionRepository
{
    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $displayName  = $request->input('display_name', null);

        try {
            $permissions = Permission::when($displayName, fn ($query) => $query->where("display_name", $displayName))
                ->orderBy("group", "ASC")
                ->paginate($paginateSize);

            // Grouping data
            // $groupedPermissions = [];
            // foreach ($permissions as $permission) {
            //     $groupedPermissions[$permission->group][] = $permission;
            // }

            return $permissions;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $permission = new Permission();

            $name  = Str::slug($request->displayName);
            $group = Str::before($name, "-");

            $permission->display_name = $request->displayName;
            $permission->name         = $name;
            $permission->group        = $group;
            $permission->description  = $request->description;
            $permission->save();

            DB::commit();

            return $permission;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $permission = Permission::with(["createdBy:id,username", "updatedBy:id,username"])->find($id);

            if (!$permission) {
                throw new CustomException('Permission not found');
            }

            return $permission;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $permission = Permission::find($id);

            if (!$permission) {
                throw new CustomException('Permission not found');
            }

            $name  = Str::slug($request->displayName);
            $group = Str::before($name, "-");

            $permission->display_name = $request->displayName;
            $permission->name         = $name;
            $permission->group        = $group;
            $permission->description  = $request->description;
            $permission->save();

            DB::commit();

            return $permission;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $permission = Permission::find($id);
            if (!$permission) {
                throw new CustomException('Permission not found');
            }

            return $permission->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
