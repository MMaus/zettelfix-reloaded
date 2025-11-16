<?php

namespace App\Policies;

use App\Models\ShoppingListItem;
use App\Models\User;

class ShoppingListItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view shopping items (unauthenticated users see public items)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, ShoppingListItem $shoppingListItem): bool
    {
        // Users can view their own items or public items (user_id = null)
        if (! $user) {
            return $shoppingListItem->user_id === null;
        }

        return $shoppingListItem->user_id === null || $shoppingListItem->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user): bool
    {
        // Anyone can create shopping items
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(?User $user, ShoppingListItem $shoppingListItem): bool
    {
        // Users can update their own items or public items
        if (! $user) {
            return $shoppingListItem->user_id === null;
        }

        return $shoppingListItem->user_id === null || $shoppingListItem->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?User $user, ShoppingListItem $shoppingListItem): bool
    {
        // Users can delete their own items or public items
        if (! $user) {
            return $shoppingListItem->user_id === null;
        }

        return $shoppingListItem->user_id === null || $shoppingListItem->user_id === $user->id;
    }
}
