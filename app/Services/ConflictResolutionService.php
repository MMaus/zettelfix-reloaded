<?php

namespace App\Services;

use App\Models\ShoppingListItem;
use App\Models\TodoItem;
use Carbon\Carbon;

class ConflictResolutionService
{
    /**
     * Resolve conflict between server and local todo item using last write wins.
     *
     * @return array Resolved item data
     */
    public function resolveTodoConflict(TodoItem $serverItem, array $localChange): array
    {
        $serverUpdatedAt = Carbon::parse($serverItem->updated_at);
        $localUpdatedAt = Carbon::parse($localChange['updated_at']);

        // Last write wins - if timestamps are equal, server wins
        if ($localUpdatedAt->greaterThan($serverUpdatedAt)) {
            return [
                'title' => $localChange['title'] ?? $serverItem->title,
                'description' => $localChange['description'] ?? $serverItem->description,
                'tags' => $localChange['tags'] ?? $serverItem->tags,
                'due_date' => $localChange['due_date'] ?? $serverItem->due_date?->toDateString(),
                'updated_at' => $localUpdatedAt,
            ];
        }

        // Server wins
        return [
            'title' => $serverItem->title,
            'description' => $serverItem->description,
            'tags' => $serverItem->tags,
            'due_date' => $serverItem->due_date?->toDateString(),
            'updated_at' => $serverUpdatedAt,
        ];
    }

    /**
     * Resolve conflict between server and local shopping item using last write wins.
     *
     * @return array Resolved item data
     */
    public function resolveShoppingItemConflict(ShoppingListItem $serverItem, array $localChange): array
    {
        $serverUpdatedAt = Carbon::parse($serverItem->updated_at);
        $localUpdatedAt = Carbon::parse($localChange['updated_at']);

        // Last write wins - if timestamps are equal, server wins
        if ($localUpdatedAt->greaterThan($serverUpdatedAt)) {
            return [
                'name' => $localChange['name'] ?? $serverItem->name,
                'quantity' => $localChange['quantity'] ?? $serverItem->quantity,
                'categories' => $localChange['categories'] ?? $serverItem->categories,
                'in_basket' => $localChange['in_basket'] ?? $serverItem->in_basket,
                'updated_at' => $localUpdatedAt,
            ];
        }

        // Server wins
        return [
            'name' => $serverItem->name,
            'quantity' => $serverItem->quantity,
            'categories' => $serverItem->categories,
            'in_basket' => $serverItem->in_basket,
            'updated_at' => $serverUpdatedAt,
        ];
    }
}
