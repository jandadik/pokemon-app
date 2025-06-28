<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LanguageEnum implements Rule
{
    protected array $allowed = [
        'english', 'czech', 'german', 'french', 'japanese'
    ];

    public function passes($attribute, $value): bool
    {
        return in_array($value, $this->allowed, true);
    }

    public function message(): string
    {
        return __('validation.custom.language.language_enum');
    }
} 