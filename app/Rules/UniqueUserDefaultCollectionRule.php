<?php

namespace App\Rules;

use App\Models\UserCollection;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class UniqueUserDefaultCollectionRule implements ValidationRule
{
    protected ?int $ignoreCollectionId;

    /**
     * Create a new rule instance.
     *
     * @param int|null $ignoreCollectionId The ID of the collection to ignore (used during updates).
     */
    public function __construct(?int $ignoreCollectionId = null)
    {
        $this->ignoreCollectionId = $ignoreCollectionId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // This rule only applies if is_default is being set to true.
        // If $value is false (or anything not strictly true), the rule passes 
        // as we are not trying to set a new default collection.
        if ($value !== true && $value !== 'true' && $value !== 1 && $value !== '1') {
            return;
        }

        $query = UserCollection::where('user_id', Auth::id())
            ->where('is_default', true);

        if ($this->ignoreCollectionId !== null) {
            $query->where('id', '!=', $this->ignoreCollectionId);
        }

        if ($query->exists()) {
            $fail('Uživatel již má nastavenou jinou výchozí kolekci.');
        }
    }
} 