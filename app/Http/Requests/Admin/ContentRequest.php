<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use App\Rules\EnumValidation;
use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->id;

        return [
            "title"       => ["required", "unique:contents,title,$id"],
            "content"     => ["required", "string"],
            "image"       => ["sometimes", "nullable", "mimes:jpeg,png,jpg,gif,webp,svg"],
            'status'      => ["nullable", new EnumValidation(StatusEnum::class)]
        ];
    }
}
