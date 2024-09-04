<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\BaseController;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\RegistrationRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\ChangePasswordRequest;

class AuthController extends BaseController
{
    protected $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function registration(RegistrationRequest $request)
    {
        try {
            $user = $this->repository->registration($request);

            return $this->sendResponse($user, 'Register successfully');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

           return $this->sendError(__("common.commonError"));
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = $this->repository->login($request);

            return $this->sendResponse($user, "Login successfully");
        } catch (CustomException $exception) {
            return $this->sendError($exception->getMessage());
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();

            if(Hash::check($request->old_password, $user->password)){

                $user = $this->repository->changePassword($request, $user);
                return $this->sendResponse($user, "Password Changed Successfully");

            } else {
                return $this->sendError("Old password doesn't match");
            }


        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = $this->repository->resetPassword($request);

            return $this->sendResponse($user, "Reset Password Request Send Successfully");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->repository->logout($request);

            return $this->sendResponse(null, "User logout successfully");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError(__("common.commonError"));
        }
    }
}
