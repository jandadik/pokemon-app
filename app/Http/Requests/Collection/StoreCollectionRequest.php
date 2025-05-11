<?php

namespace App\Http\Requests\Collection;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
// TODO: Import custom rule for is_default if created separately

class StoreCollectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Anyone authenticated can attempt to create a collection.
        // Specific business logic (e.g., max number of collections) would be handled elsewhere or via custom validation.
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'required|boolean',
            'is_default' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Název kolekce je povinný.',
            'name.string' => 'Název kolekce musí být řetězec.',
            'name.max' => 'Název kolekce může mít maximálně :max znaků.',
            'description.string' => 'Popis kolekce musí být řetězec.',
            'description.max' => 'Popis kolekce může mít maximálně :max znaků.',
            'is_public.required' => 'Nastavení viditelnosti je povinné.',
            'is_public.boolean' => 'Nastavení viditelnosti musí být boolean (true/false).',
            'is_default.boolean' => 'Nastavení výchozí kolekce musí být boolean (true/false).',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('is_public') && !is_bool($this->input('is_public'))) {
            $this->merge([
                'is_public' => filter_var($this->input('is_public'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
        if ($this->has('is_default') && !is_bool($this->input('is_default'))) {
            $this->merge([
                'is_default' => filter_var($this->input('is_default'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
} 