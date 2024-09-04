<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "username"     => ["required", "string", "max:20"],
            "phone_number" => ["required", "unique:users", "regex:/^[0-9]+$/", "digits:11"],
            "email"        => ["nullable", "email", "unique:users"],
            "password"     => ["required", "confirmed"]
        ];
    }
}
