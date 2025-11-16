<?php

use App\Models\ShoppingListItem;
use App\Models\TodoItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('authenticated user can sync todo items', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create some todo items on server
    $todo1 = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Server Todo 1',
        'synced_at' => now(),
    ]);
    $todo2 = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Server Todo 2',
        'synced_at' => now(),
    ]);

    // Simulate sync request with no local changes
    $response = $this->postJson('/sync', [
        'last_synced_at' => null,
        'local_changes' => [
            'todos' => [],
            'shopping_items' => [],
            'deleted_ids' => [
                'todos' => [],
                'shopping_items' => [],
            ],
        ],
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'todos',
        'shopping_items',
        'deleted',
        'synced_at',
    ]);

    $data = $response->json();
    expect($data['todos'])->toHaveCount(2);
    expect($data['todos'][0]['title'])->toBeIn(['Server Todo 1', 'Server Todo 2']);
});

test('authenticated user can sync local changes to server', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create a local todo item (simulated)
    $localTodo = [
        'id' => 'local-123',
        'title' => 'Local Todo',
        'description' => 'Created offline',
        'tags' => ['urgent'],
        'created_at' => now()->subHour()->toIso8601String(),
        'updated_at' => now()->subHour()->toIso8601String(),
    ];

    $response = $this->postJson('/sync', [
        'last_synced_at' => null,
        'local_changes' => [
            'todos' => [$localTodo],
            'shopping_items' => [],
            'deleted_ids' => [
                'todos' => [],
                'shopping_items' => [],
            ],
        ],
    ]);

    $response->assertStatus(200);
    
    // Verify the local todo was created on server
    $this->assertDatabaseHas('todo_items', [
        'user_id' => $user->id,
        'title' => 'Local Todo',
        'description' => 'Created offline',
    ]);
});

test('authenticated user can sync shopping list items', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create shopping items on server
    $item1 = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Milk',
        'synced_at' => now(),
    ]);
    $item2 = ShoppingListItem::factory()->create([
        'user_id' => $user->id,
        'name' => 'Bread',
        'synced_at' => now(),
    ]);

    $response = $this->postJson('/sync', [
        'last_synced_at' => null,
        'local_changes' => [
            'todos' => [],
            'shopping_items' => [],
            'deleted_ids' => [
                'todos' => [],
                'shopping_items' => [],
            ],
        ],
    ]);

    $response->assertStatus(200);
    $data = $response->json();
    expect($data['shopping_items'])->toHaveCount(2);
    expect($data['shopping_items'][0]['name'])->toBeIn(['Milk', 'Bread']);
});

test('sync handles conflict resolution with last write wins', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create item on server
    $serverItem = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Original Title',
        'updated_at' => now()->subHour(),
    ]);

    // Simulate local change with newer timestamp
    $localChange = [
        'id' => $serverItem->id,
        'title' => 'Updated Title',
        'description' => $serverItem->description,
        'updated_at' => now()->toIso8601String(),
        'created_at' => $serverItem->created_at->toIso8601String(),
    ];

    $response = $this->postJson('/sync', [
        'last_synced_at' => now()->subHours(2)->toIso8601String(),
        'local_changes' => [
            'todos' => [$localChange],
            'shopping_items' => [],
            'deleted_ids' => [
                'todos' => [],
                'shopping_items' => [],
            ],
        ],
    ]);

    $response->assertStatus(200);
    
    // Last write wins - local change should be applied
    $serverItem->refresh();
    expect($serverItem->title)->toBe('Updated Title');
});

test('sync returns only items updated since last sync', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $oldItem = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Old Item',
        'updated_at' => now()->subDays(2),
    ]);

    $newItem = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'New Item',
        'updated_at' => now(),
    ]);

    $lastSyncedAt = now()->subDay()->toIso8601String();

    $response = $this->postJson('/sync', [
        'last_synced_at' => $lastSyncedAt,
        'local_changes' => [
            'todos' => [],
            'shopping_items' => [],
            'deleted_ids' => [
                'todos' => [],
                'shopping_items' => [],
            ],
        ],
    ]);

    $response->assertStatus(200);
    $data = $response->json();
    
    // Should only return new item
    expect($data['todos'])->toHaveCount(1);
    expect($data['todos'][0]['title'])->toBe('New Item');
});

test('sync handles deleted items', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $item1 = TodoItem::factory()->create(['user_id' => $user->id]);
    $item2 = TodoItem::factory()->create(['user_id' => $user->id]);
    $item3 = TodoItem::factory()->create(['user_id' => $user->id]);

    // Delete item2 locally
    $response = $this->postJson('/sync', [
        'last_synced_at' => null,
        'local_changes' => [
            'todos' => [],
            'shopping_items' => [],
            'deleted_ids' => [
                'todos' => [$item2->id],
                'shopping_items' => [],
            ],
        ],
    ]);

    $response->assertStatus(200);
    
    // Item2 should be deleted
    $this->assertDatabaseMissing('todo_items', ['id' => $item2->id]);
    
    // Other items should still exist
    $this->assertDatabaseHas('todo_items', ['id' => $item1->id]);
    $this->assertDatabaseHas('todo_items', ['id' => $item3->id]);
});

test('sync requires authentication', function () {
    $response = $this->postJson('/sync', [
        'last_synced_at' => null,
        'local_changes' => [
            'todos' => [],
            'shopping_items' => [],
            'deleted_ids' => [
                'todos' => [],
                'shopping_items' => [],
            ],
        ],
    ]);

    $response->assertStatus(401);
});

test('sync merges local items avoiding duplicates', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create item on server
    $serverItem = TodoItem::factory()->create([
        'user_id' => $user->id,
        'title' => 'Same Title',
        'created_at' => now()->subMinutes(30),
    ]);

    // Local item with same title and similar creation time (within 1 hour window)
    $localItem = [
        'id' => 'local-123',
        'title' => 'Same Title',
        'description' => null,
        'created_at' => now()->subMinutes(25)->toIso8601String(),
        'updated_at' => now()->subMinutes(25)->toIso8601String(),
    ];

    $response = $this->postJson('/sync', [
        'last_synced_at' => null,
        'local_changes' => [
            'todos' => [$localItem],
            'shopping_items' => [],
            'deleted_ids' => [
                'todos' => [],
                'shopping_items' => [],
            ],
        ],
    ]);

    $response->assertStatus(200);
    
    // Should not create duplicate - should match with existing item
    $todoCount = TodoItem::where('user_id', $user->id)
        ->where('title', 'Same Title')
        ->count();
    
    expect($todoCount)->toBe(1);
});
