<?php

use App\Models\ShoppingListItem;
use App\Models\TodoItem;
use App\Models\User;
use App\Services\SyncService;
use Carbon\Carbon;

test('sync service merges local todos with server todos', function () {
    $user = User::factory()->create();
    $service = new SyncService(new \App\Services\ConflictResolutionService());

    // Create server todo
    $serverTodo = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Server Todo',
    ]);

    // Local todo
    $localTodo = [
        'id' => 'local-123',
        'title' => 'Local Todo',
        'description' => null,
        'created_at' => now()->toIso8601String(),
        'updated_at' => now()->toIso8601String(),
    ];

    $result = $service->syncTodos($user, null, [$localTodo]);

    expect($result)->toHaveCount(2);
    $this->assertDatabaseHas('todo_items', [
        'user_id' => $user->id,
        'title' => 'Local Todo',
    ]);
});

test('sync service filters todos by last synced timestamp', function () {
    $user = User::factory()->create();
    $service = new SyncService(new \App\Services\ConflictResolutionService());

    $oldTodo = TodoItem::factory()->create([
        'user_id' => $user->id,
        'updated_at' => now()->subDays(2),
    ]);

    $newTodo = TodoItem::factory()->create([
        'user_id' => $user->id,
        'updated_at' => now(),
    ]);

    $lastSyncedAt = now()->subDay();
    $result = $service->getTodosSince($user, $lastSyncedAt);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($newTodo->id);
});

test('sync service handles duplicate detection', function () {
    $user = User::factory()->create();
    $service = new SyncService(new \App\Services\ConflictResolutionService());

    // Create server todo
    $serverTodo = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Duplicate Title',
        'created_at' => now()->subMinutes(30),
    ]);

    // Local todo with same title and similar creation time
    $localTodo = [
        'id' => 'local-123',
        'title' => 'Duplicate Title',
        'description' => null,
        'created_at' => now()->subMinutes(25)->toIso8601String(),
        'updated_at' => now()->subMinutes(25)->toIso8601String(),
    ];

    $isDuplicate = $service->isDuplicateTodo($user, $localTodo);

    expect($isDuplicate)->toBeTrue();
});

test('sync service creates non-duplicate local todos', function () {
    $user = User::factory()->create();
    $service = new SyncService(new \App\Services\ConflictResolutionService());

    $localTodo = [
        'id' => 'local-123',
        'title' => 'Unique Todo',
        'description' => null,
        'created_at' => now()->toIso8601String(),
        'updated_at' => now()->toIso8601String(),
    ];

    $isDuplicate = $service->isDuplicateTodo($user, $localTodo);

    expect($isDuplicate)->toBeFalse();
});

