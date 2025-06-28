<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Rules\ConditionEnum;
use App\Rules\LanguageEnum;
use App\Rules\GradingRequiredWith;

class CollectionItemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Uživatel musí být přihlášen
        return Auth::check();
    }

    public function rules(): array
    {
        // card_id a variant_id se při update nemění, takže nejsou součástí validace formuláře.
        // Validují se pouze data, která může uživatel změnit.
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'condition' => ['required', 'string', 'max:32', new ConditionEnum()],
            'language' => ['required', 'string', 'max:10', new LanguageEnum()],
            'first_edition' => ['nullable', 'boolean'],
            'grading' => ['nullable', 'string', 'max:50'],
            'grading_cert' => ['nullable', 'string', 'max:10'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            'purchase_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:65535'],
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
        });
    }
} 