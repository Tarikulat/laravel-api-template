<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use App\Classes\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\CustomException;

class UserRepository
{
    public function index($request)
    {
        $paginateSize = $request->input('paginate_size', null);
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input('name', null);
        $phoneNumber  = $request->input('phone_number', null);

        try {
            $users = User::with('roles')
                ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
                ->when($phoneNumber, fn($query) => $query->where("phone_number", "like", "%$phoneNumber%"))
                ->orderBy('created_at', 'desc')->paginate($paginateSize);

            return $users;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $user = new User();

            $user->username     = $request->username;
            $user->email        = $request->email;
            $user->phone_number = $request->phone_number;
            $user->status       = $request->status;
            $user->password     = Hash::make($request->password);
            $user->save();
            if ($request->role_ids && count($request->role_ids) > 0) {
                $user->syncRoles($request->role_ids);
            }

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function show($id)
    {
        try {
            $user = User::with("roles:id,name,display_name", "roles.permissions:id,name,display_name")->find($id);

            if (!$user) {
                throw new CustomException("User not found");
            }

            return $user;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $user = User::find($id);
            if (!$user) {
                throw new CustomException('User not found');
            }

            $user->username     = $request->username;
            $user->email        = $request->email;
            $user->phone_number = $request->phone_number;
            $user->status       = $request->status;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            if ($request->role_ids && count($request->role_ids) > 0) {
                $user->syncRoles($request->role_ids);
            }

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                throw new CustomException('User not found');
            }

            return $user->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function approvalsList()
    {
        try {
            $users = User::where('is_verified', null)->get();

            return $users;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function approvalUpdate($request)
    {
        try {

            DB::beginTransaction();

            $user = User::find($request->id);
            if (!$user) {
                throw new CustomException('User not found');
            }

            $user->designation_id = $request->designation_id;
            $user->status         = 'active';
            $user->is_verified    = 1;
            $user->save();

            if ($request->role_ids && count($request->role_ids) > 0) {
                $user->syncRoles($request->role_ids);
            }

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}




