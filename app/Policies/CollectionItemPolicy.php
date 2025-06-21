<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserCollection;
use App\Models\UserCollectionItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectionItemPolicy
{
    use HandlesAuthorization;

    /**
     * Zda může uživatel zobrazit položku sbírky.
     */
    public function view(?User $user, UserCollectionItem $item): bool
    {
        // Veřejné položky nebo vlastník kolekce
        return $item->collection && (
            $item->collection->is_public ||
            ($user && $user->id === $item->collection->user_id)
        );
    }

    /**
     * Zda může uživatel vytvořit položku v kolekci.
     */
    public function create(User $user, UserCollection $collection): bool
    {
        return $user->id === $collection->user_id;
    }

    /**
     * Zda může uživatel upravit položku sbírky.
     */
    public function update(User $user, UserCollectionItem $item): bool
    {
        return $item->collection && $user->id === $item->collection->user_id;
    }

    /**
     * Zda může uživatel smazat položku sbírky.
     */
    public function delete(User $user, UserCollectionItem $item): bool
    {
        return $item->collection && $user->id === $item->collection->user_id;
    }
} 