<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LogicException;
use Illuminate\Support\Facades\Gate;

class CollectionService
{
    /**
     * Create a new collection for a user.
     * Handles setting the new collection as default if specified,
     * ensuring other collections for the user are not default.
     *
     * @param User $user The user creating the collection.
     * @param array $data Data for the new collection (name, description, is_public, is_default).
     *                    Validated data should be passed from the controller.
     * @return UserCollection
     */
    public function createCollection(User $user, array $data): UserCollection
    {
        return DB::transaction(function () use ($user, $data) {
            $isDefault = $data['is_default'] ?? false;

            if ($isDefault) {
                // Set all other collections of this user to not be default
                $user->collections()->update(['is_default' => false]);
            }

            // Ensure 'is_default' in data reflects the intended state
            $collectionData = array_merge($data, ['is_default' => $isDefault]);

            return $user->collections()->create($collectionData);
        });
    }

    /**
     * Retrieve a specific collection by its ID.
     * Verifies ownership or public visibility using Laravel Policies if possible.
     *
     * @param int $collectionId The ID of the collection.
     * @param User|null $requestingUser The user requesting the collection (can be null for public access).
     * @return UserCollection
     * @throws ModelNotFoundException If the collection is not found.
     * @throws AuthorizationException If the user is not authorized to view the collection.
     */
    public function getCollectionById(int $collectionId, ?User $requestingUser): UserCollection
    {
        $collection = UserCollection::find($collectionId);

        if (!$collection) {
            throw new ModelNotFoundException("Collection with ID {$collectionId} not found.");
        }

        if ($requestingUser) {
            Gate::forUser($requestingUser)->authorize('view', $collection);
        } else {
            // For guest users, Gate::authorize will pass null as the user to the policy method
            Gate::authorize('view', $collection);
        }

        return $collection;
    }

    /**
     * Update an existing collection.
     * Verifies ownership using Laravel Policies. Handles updating the 'is_default' status.
     *
     * @param UserCollection $collection The collection to update.
     * @param array $data New data for the collection. Validated data from controller.
     * @param User $requestingUser The user attempting the update (used for policy check).
     * @return UserCollection
     * @throws AuthorizationException if not owner (handled by Policy).
     */
    public function updateCollection(UserCollection $collection, array $data, User $requestingUser): UserCollection
    {
        Gate::forUser($requestingUser)->authorize('update', $collection);

        return DB::transaction(function () use ($collection, $data, $requestingUser) {
            if (array_key_exists('is_default', $data)) {
                if ($data['is_default'] === true) {
                    $this->setCollectionAsDefault($collection, $requestingUser);
                } else {
                    // Pokud se is_default nastavuje na false.
                    // Validace, že nerušíme poslední výchozí, je nyní v UpdateCollectionRequest.
                    // Pokud je toto jediná výchozí kolekce, FormRequest by měl selhat dříve, než se sem dostaneme.
                    // Služba tedy jen provede změnu, pokud validace prošla.
                    $collection->is_default = false;
                }
                // Odstraníme is_default z $data, aby se neaplikovalo znovu přes fill()
                unset($data['is_default']);
            }

            if (!empty($data)) {
                $collection->fill($data);
            }

            $collection->save();
            return $collection->fresh();
        });
    }

    /**
     * Delete a collection.
     * Verifies ownership using Laravel Policies.
     *
     * @param UserCollection $collection The collection to delete.
     * @param User $requestingUser The user attempting the deletion (used for policy check).
     * @return bool True on success.
     * @throws AuthorizationException if not owner (handled by Policy).
     */
    public function deleteCollection(UserCollection $collection, User $requestingUser): bool
    {
        // Authorize using UserCollectionPolicy@delete
        Gate::forUser($requestingUser)->authorize('delete', $collection);

        return DB::transaction(function () use ($collection, $requestingUser) {
            if ($collection->is_default) {
                $otherCollections = $requestingUser->collections()
                    ->where('id', '!=', $collection->id)
                    ->orderByDesc('created_at')
                    ->get();

                if ($otherCollections->isNotEmpty()) {
                    $newDefaultCollection = $otherCollections->first();
                    // No need to authorize again for 'setDefault', 
                    // setCollectionAsDefault will do its own 'setDefault' check.
                    $this->setCollectionAsDefault($newDefaultCollection, $requestingUser);
                } else {
                    // This is the only collection. Deleting it means the user will have no collections.
                }
            }

            return $collection->delete();
        });
    }

    /**
     * Set a specific collection as the default for the user.
     * Ensures only one collection is default for the user at any time.
     * Verifies ownership using Laravel Policies.
     *
     * @param UserCollection $collectionToMakeDefault The collection to be set as default.
     * @param User $user The owner of the collection (used for policy check).
     * @return UserCollection
     * @throws AuthorizationException if $user does not own $collectionToMakeDefault (handled by Policy).
     */
    public function setCollectionAsDefault(UserCollection $collectionToMakeDefault, User $user): UserCollection
    {
        // Authorize using UserCollectionPolicy@setDefault
        Gate::forUser($user)->authorize('setDefault', $collectionToMakeDefault);

        DB::transaction(function () use ($collectionToMakeDefault, $user) {
            // Set all other collections of this user to not be default
            $user->collections()
                 ->where('id', '!=', $collectionToMakeDefault->id)
                 ->update(['is_default' => false]);

            // Set the specified collection as default
            $collectionToMakeDefault->is_default = true;
            $collectionToMakeDefault->save();
        });

        return $collectionToMakeDefault->fresh(); // Return fresh model state
    }

    /**
     * Set the visibility of a specific collection.
     *
     * @param UserCollection $collection The collection to update.
     * @param User $user The owner of the collection (used for policy check).
     * @param bool $isPublic The new visibility status.
     * @return UserCollection
     * @throws AuthorizationException if $user is not authorized to update $collection.
     */
    public function setCollectionVisibility(UserCollection $collection, User $user, bool $isPublic): UserCollection
    {
        Gate::forUser($user)->authorize('update', $collection);

        $collection->is_public = $isPublic;
        $collection->save();

        return $collection->fresh();
    }

    /**
     * Get all collections for a specific user.
     *
     * @param User $user The user whose collections to retrieve.
     * @param array $filters Optional filters (e.g., ['is_public' => true/false]).
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserCollections(User $user, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $user->collections();

        if (isset($filters['is_public'])) {
            $query->where('is_public', (bool) $filters['is_public']);
        }

        // Add other filters as needed based on $filters array

        return $query->orderBy('name')->paginate(10);
    }

    /**
     * List all public collections.
     *
     * @param array $filters Optional filters.
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPublicCollections(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = UserCollection::where('is_public', true);

        // Example of a potential filter for public collections, e.g., by name part
        if (isset($filters['name_contains'])) {
            $query->where('name', 'like', '%' . $filters['name_contains'] . '%');
        }

        // Add other filters as needed

        return $query->orderBy('name')->paginate(10); // Default ordering by name
    }
} 