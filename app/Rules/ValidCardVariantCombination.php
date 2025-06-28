<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\CardsVariant;

class ValidCardVariantCombination implements Rule
{
    protected $cardId;

    public function __construct($cardId)
    {
        $this->cardId = $cardId;
    }

    public function passes($attribute, $value): bool
    {
        if (!$this->cardId || !$value) {
            return false;
        }
        $variant = CardsVariant::where('cm_id', $value)->first();
        if (!$variant) {
            return false;
        }
        return $variant->card_id == $this->cardId;
    }

    public function message(): string
    {
        return __('validation.custom.variant_id.valid_card_variant_combination');
    }
} 