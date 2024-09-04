<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumValidation implements Rule
{
    protected string $enum;

    public function __construct(string $enum)
    {
        $this->enum = $enum;
    }

    public function passes($attribute, $value)
    {
        if (!enum_exists($this->enum)) {
            return false;
        }

        return $this->enum::tryFrom($value) !== null;
    }

    public function message()
    {
        return 'The selected :attribute is invalid.';
    }
}

