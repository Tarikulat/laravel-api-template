<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\PermissionRepository;
use App\Http\Resources\Admin\PermissionResource;
use App\Http\Resources\Admin\PermissionCollection;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;

class PermissionController extends BaseController
{
    protected $repository;

    public function __construct(PermissionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('permissions-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $permissions = $this->repository->index($request);

            $permissions = new PermissionCollection($permissions);

            return $this->sendResponse($permissions, 'Permission list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StorePermissionRequest $request)
    {
        if (!$request->user()->hasPermission('permissions-create')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $permission = $this->repository->store($request);

            $permission = new PermissionResource($permission);

            return $this->sendResponse($permission, 'Permission created successfully');

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('permissions-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $permission = $this->repository->show($id);

            $permission = new PermissionResource($permission);

            return $this->sendResponse($permission, 'Permission single view');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
        if (!$request->user()->hasPermission('permissions-update')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $permission = $this->repository->update($request, $id);

            $permission = new PermissionResource($permission);

            return $this->sendResponse($permission, 'Permission updated successfully');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('permissions-delete')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $permission = $this->repository->delete($id);

            return $this->sendResponse($permission, 'permission deleted successfully');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
