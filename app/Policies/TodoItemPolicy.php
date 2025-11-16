<?php

namespace App\Policies;

use App\Models\TodoItem;
use App\Models\User;

class TodoItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view todos (unauthenticated users see public todos)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, TodoItem $todoItem): bool
    {
        // Users can view their own todos or public todos (user_id = null)
        if (! $user) {
            return $todoItem->user_id === null;
        }

        return $todoItem->user_id === null || $todoItem->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user): bool
    {
        // Anyone can create todos
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(?User $user, TodoItem $todoItem): bool
    {
        // Users can update their own todos or public todos
        if (! $user) {
            return $todoItem->user_id === null;
        }

        return $todoItem->user_id === null || $todoItem->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(?User $user, TodoItem $todoItem): bool
    {
        // Users can delete their own todos or public todos
        if (! $user) {
            return $todoItem->user_id === null;
        }

        return $todoItem->user_id === null || $todoItem->user_id === $user->id;
    }
}
