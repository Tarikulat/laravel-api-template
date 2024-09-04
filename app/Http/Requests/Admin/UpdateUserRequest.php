<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            'username'     => ['required'],
            'phone_number' => ['required', "unique:users,phone_number,$id"],
            'email'        => ['nullable', "unique:users,email,$id"],
            'role_ids'     => ['required', 'array']
        ];
    }
}
