<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class GradingRequiredWith implements Rule
{
    protected string $otherField;

    public function __construct(string $otherField)
    {
        $this->otherField = $otherField;
    }

    public function passes($attribute, $value): bool
    {
        $other = request()->input($this->otherField);
        // Pokud je grading vyplněn, cert musí být také, a naopak
        if (!empty($value) && empty($other)) {
            return false;
        }
        if (!empty($other) && empty($value)) {
            return false;
        }
        return true;
    }

    public function message(): string
    {
        return __('validation.custom.' . $this->otherField . '.grading_required_with');
    }
} 