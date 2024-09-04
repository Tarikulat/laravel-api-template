<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
         'display_name'   => ['required', "unique:roles,display_name"],
         'permission_ids' => ['required', 'array']
        ];
    }
}
