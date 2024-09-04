<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'     => ['required'],
            'phone_number' => ['required', "unique:users,phone_number"],
            'email'        => ['nullable', "unique:users,email"],
            'password'     => ['required'],
            'role_ids'     => ['required', 'array']
        ];
    }
}
