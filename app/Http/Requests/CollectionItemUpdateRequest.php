<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
            'condition' => ['required', 'string', 'max:32'],
            'language' => ['required', 'string', 'max:16'],
            'first_edition' => ['present', 'boolean'],
            'grading' => ['nullable', 'string', 'max:32'],
            'grading_cert' => ['nullable', 'string', 'max:64', Rule::requiredIf(!empty($this->grading))],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'location' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
} 