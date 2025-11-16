<?php

namespace App\Services;

use App\Models\ShoppingListItem;
use App\Models\TodoItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SyncService
{
    protected ConflictResolutionService $conflictResolver;

    public function __construct(ConflictResolutionService $conflictResolver)
    {
        $this->conflictResolver = $conflictResolver;
    }

    /**
     * Get todos updated since last sync timestamp.
     */
    public function getTodosSince(User $user, ?Carbon $lastSyncedAt): Collection
    {
        $query = TodoItem::where('user_id', $user->id);

        if ($lastSyncedAt) {
            $query->where('updated_at', '>', $lastSyncedAt);
        }

        return $query->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Get shopping items updated since last sync timestamp.
     */
    public function getShoppingItemsSince(User $user, ?Carbon $lastSyncedAt): Collection
    {
        $query = ShoppingListItem::where('user_id', $user->id);

        if ($lastSyncedAt) {
            $query->where('updated_at', '>', $lastSyncedAt);
        }

        return $query->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Sync local todos to server, handling conflicts and duplicates.
     */
    public function syncTodos(User $user, ?Carbon $lastSyncedAt, array $localTodos): Collection
    {
        foreach ($localTodos as $localTodo) {
            // Check if this is an update to existing item
            if (isset($localTodo['id']) && is_numeric($localTodo['id'])) {
                $serverItem = TodoItem::where('user_id', $user->id)
                    ->find($localTodo['id']);

                if ($serverItem) {
                    // Conflict resolution
                    $resolved = $this->conflictResolver->resolveTodoConflict($serverItem, $localTodo);
                    $serverItem->update($resolved);

                    continue;
                }
            }

            // Check for duplicates (same title + creation time within 1 hour)
            if ($this->isDuplicateTodo($user, $localTodo)) {
                continue;
            }

            // Create new item
            TodoItem::create([
                'user_id' => $user->id,
                'title' => $localTodo['title'],
                'description' => $localTodo['description'] ?? null,
                'tags' => $localTodo['tags'] ?? null,
                'due_date' => isset($localTodo['due_date']) ? Carbon::parse($localTodo['due_date']) : null,
                'created_at' => Carbon::parse($localTodo['created_at']),
                'updated_at' => Carbon::parse($localTodo['updated_at']),
            ]);
        }

        return $this->getTodosSince($user, $lastSyncedAt);
    }

    /**
     * Check if local todo is a duplicate of existing server todo.
     */
    public function isDuplicateTodo(User $user, array $localTodo): bool
    {
        $localCreatedAt = Carbon::parse($localTodo['created_at']);
        $oneHourAgo = $localCreatedAt->copy()->subHour();
        $oneHourLater = $localCreatedAt->copy()->addHour();

        return TodoItem::where('user_id', $user->id)
            ->where('title', $localTodo['title'])
            ->whereBetween('created_at', [$oneHourAgo, $oneHourLater])
            ->exists();
    }

    /**
     * Sync local shopping items to server, handling conflicts and duplicates.
     */
    public function syncShoppingItems(User $user, ?Carbon $lastSyncedAt, array $localItems): Collection
    {
        foreach ($localItems as $localItem) {
            // Check if this is an update to existing item
            if (isset($localItem['id']) && is_numeric($localItem['id'])) {
                $serverItem = ShoppingListItem::where('user_id', $user->id)
                    ->find($localItem['id']);

                if ($serverItem) {
                    // Conflict resolution
                    $resolved = $this->conflictResolver->resolveShoppingItemConflict($serverItem, $localItem);
                    $serverItem->update($resolved);

                    continue;
                }
            }

            // Check for duplicates (same name + creation time within 1 hour)
            if ($this->isDuplicateShoppingItem($user, $localItem)) {
                continue;
            }

            // Create new item
            ShoppingListItem::create([
                'user_id' => $user->id,
                'name' => $localItem['name'],
                'quantity' => $localItem['quantity'] ?? 1.0,
                'categories' => $localItem['categories'] ?? null,
                'in_basket' => $localItem['in_basket'] ?? false,
                'created_at' => Carbon::parse($localItem['created_at']),
                'updated_at' => Carbon::parse($localItem['updated_at']),
            ]);
        }

        return $this->getShoppingItemsSince($user, $lastSyncedAt);
    }

    /**
     * Check if local shopping item is a duplicate of existing server item.
     */
    public function isDuplicateShoppingItem(User $user, array $localItem): bool
    {
        $localCreatedAt = Carbon::parse($localItem['created_at']);
        $oneHourAgo = $localCreatedAt->copy()->subHour();
        $oneHourLater = $localCreatedAt->copy()->addHour();

        return ShoppingListItem::where('user_id', $user->id)
            ->where('name', $localItem['name'])
            ->whereBetween('created_at', [$oneHourAgo, $oneHourLater])
            ->exists();
    }

    /**
     * Delete items by IDs.
     */
    public function deleteItems(User $user, array $todoIds, array $shoppingItemIds): void
    {
        if (! empty($todoIds)) {
            TodoItem::where('user_id', $user->id)
                ->whereIn('id', $todoIds)
                ->delete();
        }

        if (! empty($shoppingItemIds)) {
            ShoppingListItem::where('user_id', $user->id)
                ->whereIn('id', $shoppingItemIds)
                ->delete();
        }
    }
}
