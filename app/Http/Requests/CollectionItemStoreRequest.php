<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Rules\ConditionEnum;
use App\Rules\LanguageEnum;
use App\Rules\GradingRequiredWith;
use App\Rules\ValidCardVariantCombination;

class CollectionItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Uživatel musí být přihlášen
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'card_id' => ['required', 'string', 'max:20', Rule::exists('cards', 'id')],
            'variant_id' => ['nullable', 'integer', Rule::exists('cards_variant', 'cm_id')],
            'variant_type' => ['nullable', 'string', 'max:32'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'condition' => ['required', 'string', 'max:32', new ConditionEnum()],
            'language' => ['required', 'string', 'max:10', new LanguageEnum()],
            'first_edition' => ['nullable', 'boolean'],
            'grading' => ['nullable', 'string', 'max:50'],
            'grading_cert' => ['nullable', 'string', 'max:10'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            'purchase_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:65535'], // TEXT field
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();
            
            // Cross-field: grading/grading_cert
            if (!empty($data['grading']) && empty($data['grading_cert'])) {
                $validator->errors()->add('grading_cert', __('validation.custom.grading_cert.grading_required_with'));
            }
            if (!empty($data['grading_cert']) && empty($data['grading'])) {
                $validator->errors()->add('grading', __('validation.custom.grading.grading_required_with'));
            }
            
            // Cross-field: card_id + variant_id
            if (!empty($data['card_id']) && !empty($data['variant_id'])) {
                $variant = \App\Models\CardsVariant::where('cm_id', $data['variant_id'])->first();
                if (!$variant || $variant->card_id !== $data['card_id']) {
                    $validator->errors()->add('variant_id', __('validation.custom.variant_id.valid_card_variant_combination'));
                }
            }
        });
    }
} 