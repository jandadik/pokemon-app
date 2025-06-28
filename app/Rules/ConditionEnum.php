<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ConditionEnum implements Rule
{
    protected array $allowed = [
        'near_mint', 'excellent', 'good', 'played', 'poor'
    ];

    public function passes($attribute, $value): bool
    {
        return in_array($value, $this->allowed, true);
    }

    public function message(): string
    {
        return __('validation.custom.condition.condition_enum');
    }
} 