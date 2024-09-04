<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use App\Http\Requests\Admin\ApprovalUpdateRequest;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\UserCollection;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;

class UserController extends BaseController
{
     protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        if (!$request->user()->hasPermission('users-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $users = $this->repository->index($request);

            $users = new UserCollection($users);

            return $this->sendResponse($users, 'User list');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function store(StoreUserRequest $request)
    {
        if (!$request->user()->hasPermission('users-create')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $user = $this->repository->store($request);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User updated successfully');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->hasPermission('users-read')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $user = $this->repository->show($id);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User single view');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        }catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        if (!$request->user()->hasPermission('users-update')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $user = $this->repository->update($request, $id);

            $user = new UserResource($user);

            return $this->sendResponse($user, 'User updated successfully');
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function userPermission()
    {
        $id = auth()->id();

        try {
            $user = User::with("roles:id,name,display_name", "roles.permissions:id,name,display_name")->find($id);

            return $this->sendResponse($user, 'User permissions');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->hasPermission('users-delete')) {
            return $this->sendError(__("common.unauthorized"));
        }

        try {
            $user = $this->repository->delete($id);

            return $this->sendResponse(null, 'User deleted successfully');
        }catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function approvalsList(Request $request)
    {
        if (!$request->user()->hasPermission("approvals-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $approvals = $this->repository->approvalsList();

            $approvals = new UserCollection($approvals);

            return $this->sendResponse($approvals, 'Approval Request List');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function approvalUpdate(ApprovalUpdateRequest $request)
    {
        if (!$request->user()->hasPermission("approvals-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $user = $this->repository->approvalUpdate($request);

            return $this->sendResponse($user, "Request Approved Successfully");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function resetPasswordRequestList(Request $request)
    {
        if (!$request->user()->hasPermission("reset-password-read")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            $user = User::whereNotNull('tmp_password')->get();

            return $this->sendResponse($user, "Reset Password Request List");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function resetPasswordApproval(Request $request)
    {
        if (!$request->user()->hasPermission("reset-password-update")) {
            return $this->sendError(__("common.unauthorized"), 401);
        }

        try {
            if($request->approve_id){

                $user = User::where('id', $request->approve_id)->first();
                $user->password = $user->tmp_password;
                $user->tmp_password = null;
                $user->save();

                return $this->sendResponse($user, 'User Password Changed Successfully');
            }else{

                $user = User::where('id', $request->remove_id)->first();
                $user->tmp_password = null;
                $user->save();
                return $this->sendResponse($user, 'User Password Changed Request Removed');

            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
