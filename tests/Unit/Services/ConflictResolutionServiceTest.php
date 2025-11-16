<?php

use App\Models\TodoItem;
use App\Models\User;
use App\Services\ConflictResolutionService;
use Carbon\Carbon;

test('conflict resolution service uses last write wins', function () {
    $user = User::factory()->create();
    $service = new ConflictResolutionService();

    $serverItem = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Server Title',
        'updated_at' => now()->subHour(),
    ]);

    $localChange = [
        'id' => $serverItem->id,
        'title' => 'Local Title',
        'updated_at' => now()->toIso8601String(),
    ];

    $resolved = $service->resolveTodoConflict($serverItem, $localChange);

    // Last write wins - local change is newer
    expect($resolved['title'])->toBe('Local Title');
});

test('conflict resolution prefers server when timestamps are equal', function () {
    $user = User::factory()->create();
    $service = new ConflictResolutionService();

    $now = now();
    $serverItem = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Server Title',
        'updated_at' => $now,
    ]);

    $localChange = [
        'id' => $serverItem->id,
        'title' => 'Local Title',
        'updated_at' => $now->toIso8601String(),
    ];

    $resolved = $service->resolveTodoConflict($serverItem, $localChange);

    // Server wins when timestamps are equal
    expect($resolved['title'])->toBe('Server Title');
});

test('conflict resolution handles shopping list items', function () {
    $user = User::factory()->create();
    $service = new ConflictResolutionService();

    $serverItem = \App\Models\ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Server Name',
        'quantity' => 1,
        'updated_at' => now()->subHour(),
    ]);

    $localChange = [
        'id' => $serverItem->id,
        'name' => 'Local Name',
        'quantity' => 2,
        'updated_at' => now()->toIso8601String(),
    ];

    $resolved = $service->resolveShoppingItemConflict($serverItem, $localChange);

    expect($resolved['name'])->toBe('Local Name');
    expect($resolved['quantity'])->toBe(2);
});

