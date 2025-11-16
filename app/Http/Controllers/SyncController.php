<?php

namespace App\Http\Controllers;

use App\Services\SyncService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    protected SyncService $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Synchronize data between client and server.
     */
    public function sync(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'last_synced_at' => 'nullable|date',
            'local_changes' => 'required|array',
            'local_changes.todos' => 'array',
            'local_changes.shopping_items' => 'array',
            'local_changes.deleted_ids' => 'array',
            'local_changes.deleted_ids.todos' => 'array',
            'local_changes.deleted_ids.shopping_items' => 'array',
        ]);

        $lastSyncedAt = isset($validated['last_synced_at'])
            ? Carbon::parse($validated['last_synced_at'])
            : null;

        // Process local changes
        $localTodos = $validated['local_changes']['todos'] ?? [];
        $localShoppingItems = $validated['local_changes']['shopping_items'] ?? [];
        $deletedTodoIds = $validated['local_changes']['deleted_ids']['todos'] ?? [];
        $deletedShoppingItemIds = $validated['local_changes']['deleted_ids']['shopping_items'] ?? [];

        // Sync local todos to server
        if (! empty($localTodos)) {
            $this->syncService->syncTodos($user, $lastSyncedAt, $localTodos);
        }

        // Sync local shopping items to server
        if (! empty($localShoppingItems)) {
            $this->syncService->syncShoppingItems($user, $lastSyncedAt, $localShoppingItems);
        }

        // Delete items
        if (! empty($deletedTodoIds) || ! empty($deletedShoppingItemIds)) {
            $this->syncService->deleteItems($user, $deletedTodoIds, $deletedShoppingItemIds);
        }

        // Get updated items from server
        $todos = $this->syncService->getTodosSince($user, $lastSyncedAt);
        $shoppingItems = $this->syncService->getShoppingItemsSince($user, $lastSyncedAt);

        // Get deleted items (items that existed before but don't exist now)
        $deletedTodos = [];
        $deletedShoppingItems = [];

        if ($lastSyncedAt) {
            // This is a simplified approach - in production, you might want to track deletions explicitly
            // For now, we'll return empty arrays for deleted items
        }

        // Update synced_at timestamps
        $now = now();
        $todos->each(function ($todo) use ($now) {
            $todo->update(['synced_at' => $now]);
        });
        $shoppingItems->each(function ($item) use ($now) {
            $item->update(['synced_at' => $now]);
        });

        return response()->json([
            'todos' => $todos->map(function ($todo) {
                return [
                    'id' => $todo->id,
                    'user_id' => $todo->user_id,
                    'title' => $todo->title,
                    'description' => $todo->description,
                    'tags' => $todo->tags,
                    'due_date' => $todo->due_date?->toDateString(),
                    'created_at' => $todo->created_at->toIso8601String(),
                    'updated_at' => $todo->updated_at->toIso8601String(),
                    'synced_at' => $todo->synced_at?->toIso8601String(),
                ];
            })->values(),
            'shopping_items' => $shoppingItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'name' => $item->name,
                    'quantity' => (float) $item->quantity,
                    'categories' => $item->categories,
                    'in_basket' => $item->in_basket,
                    'created_at' => $item->created_at->toIso8601String(),
                    'updated_at' => $item->updated_at->toIso8601String(),
                    'synced_at' => $item->synced_at?->toIso8601String(),
                ];
            })->values(),
            'deleted' => [
                'todos' => $deletedTodos,
                'shopping_items' => $deletedShoppingItems,
            ],
            'synced_at' => $now->toIso8601String(),
        ]);
    }
}
