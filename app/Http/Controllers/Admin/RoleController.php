<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\RoleRepository;
use App\Http\Resources\Admin\RoleResource;
use App\Http\Resources\Admin\RoleCollection;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;

class RoleController extends BaseController
{
    protected $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }
    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('roles-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $roles = $this->repository->index($request);

            $roles = new RoleCollection($roles);

            return $this->sendResponse($roles, 'Role list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StoreRoleRequest $request)
    {
        if (!$request->user()->hasPermission('roles-create')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $role = $this->repository->store($request);

            $role = new RoleResource($role);

            return $this->sendResponse($role, 'Role created successfully');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', 'Something went wrong');
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('roles-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $role = $this->repository->show($id);

            $role = new RoleResource($role);

            return $this->sendResponse($role, 'Role single view');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        if (!$request->user()->hasPermission('roles-update')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $role = $this->repository->update($request, $id);

            $role = new RoleResource($role);

            return $this->sendResponse($role, 'Role updated successfully');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return back()->with('error', 'Something went wrong');
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('roles-delete')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $role = $this->repository->delete($id);

            return $this->sendResponse($role, 'Role deleted successfully');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
