<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
// use App\Classes\Helper;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function registration($request)
    {
        try {
            DB::beginTransaction();

            $user = new User();

            $user->first_name        = $request->first_name;
            $user->last_name         = $request->last_name;
            $user->username          = $request->username;
            $user->email             = $request->email;
            $user->phone_number      = $request->phone_number;
            $user->present_address   = $request->present_address;
            $user->permanent_address = $request->permanent_address;
            $user->gender            = $request->gender;
            // $user->verification_otp  = Helper::getOtpCode();
            $user->password          = Hash::make($request->password);
            $user->save();

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            DB::rollBack();

           throw $exception;
        }
    }
    public function login($request)
    {
        try {
            $user = User::where('phone_number', $request->phone_number)->first();

            if ($user) {
                if($user->is_verified){
                    if (Hash::check($request->password, $user->password)) {
                        $token =  $user->createToken('auth_token')->plainTextToken;
                        $data = [
                            'user'  => $user,
                            'token' => $token
                        ];

                        return $data;
                    } else {
                        throw new CustomException("User credential doesn't match");
                    }
                }else{
                    throw new CustomException("Your Account Not Approved Right Now.");
                }
            } else {
                throw new CustomException("User credential doesn't match");
            }

        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }
    }

    public function changePassword($request, $user)
    {
        try {

            $user->password = Hash::make($request->new_password);
            $user->save();

            return $user;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function resetPassword($request)
    {
        try {
            $user = User::where('phone_number', $request->phone_number)->first();
            if(!$user){
                throw new CustomException('User not found', 404);
            }

            $user->tmp_password = $request->password;
            $user->save();

            return $user;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function logout($request)
    {
        try {
            return $request->user()->tokens()->delete();
        } catch (Exception $exception) {

            throw $exception;
        }
    }
}
