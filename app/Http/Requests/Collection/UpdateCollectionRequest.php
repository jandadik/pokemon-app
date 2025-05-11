<?php

namespace App\Http\Requests\Collection;

use App\Models\UserCollection; // Corrected model name
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCollectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User must be authenticated to attempt an update
        if (!Auth::check()) {
            return false;
        }

        // The policy (called by authorizeResource in the controller) will handle 
        // if the authenticated user can update this specific collection.
        // So, if the user is authenticated, we allow the request to proceed to the policy check.
        
        // $collection = $this->route('collection');
        // if (!($collection instanceof UserCollection)) {
        //     // This case should be handled by Laravel's implicit model binding if type-hinted in controller
        //     return false;
        // }
        // Explicit check for ownership was here, but it's better handled by the Policy:
        // return $collection->user_id === Auth::id();
        
        // Let the controller's authorizeResource and the associated policy handle instance-specific authorization.
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // $collectionId = ($this->route('collection') instanceof UserCollection) ? $this->route('collection')->id : null; // $collectionId zde nepotřebujeme pro základní pravidla

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'description' => 'sometimes|nullable|string|max:1000',
            'is_public' => 'sometimes|required|boolean',
            'is_default' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Pravidlo se aplikuje pouze pokud je 'is_default' explicitně nastaveno na false
            if ($this->input('is_default') === false || $this->input('is_default') === 'false' || $this->input('is_default') === 0 || $this->input('is_default') === '0') {
                /** @var UserCollection|null $collectionBeingUpdated */
                $collectionBeingUpdated = $this->route('collection');
                
                if ($collectionBeingUpdated && $collectionBeingUpdated->is_default) {
                    // Uživatel se snaží zrušit výchozí stav u kolekce, která je aktuálně výchozí
                    $defaultCollectionsCount = UserCollection::where('user_id', Auth::id())
                        ->where('is_default', true)
                        ->count();

                    if ($defaultCollectionsCount <= 1) {
                        $validator->errors()->add('is_default', 'Nelze zrušit poslední výchozí kolekci. Nejprve nastavte jinou kolekci jako výchozí.');
                    }
                }
            }
        });
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
            // 'name.unique' => 'Kolekce s tímto názvem již existuje.',
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