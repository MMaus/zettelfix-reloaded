<?php

use App\Models\TodoItem;
use App\Models\User;

test('POST requests work without CSRF token', function () {
    $data = [
        'title' => 'Test Todo Without CSRF',
        'description' => 'This should work without CSRF token',
    ];

    $response = $this->post('/todos', $data);

    // Should not return 419 CSRF Token Mismatch
    $response->assertStatus(302); // Redirect after successful POST
    $this->assertDatabaseHas('todo_items', [
        'title' => 'Test Todo Without CSRF',
    ]);
});

test('PUT requests work without CSRF token', function () {
    $todo = TodoItem::factory()->create([
        'title' => 'Original Title',
    ]);

    $response = $this->put("/todos/{$todo->id}", [
        'title' => 'Updated Title Without CSRF',
        'description' => $todo->description,
    ]);

    // Should not return 419 CSRF Token Mismatch
    $response->assertStatus(302); // Redirect after successful PUT
    $this->assertDatabaseHas('todo_items', [
        'id' => $todo->id,
        'title' => 'Updated Title Without CSRF',
    ]);
});

test('DELETE requests work without CSRF token', function () {
    $todo = TodoItem::factory()->create([
        'title' => 'Todo To Delete',
    ]);

    $todoId = $todo->id;

    $response = $this->delete("/todos/{$todoId}");

    // Should not return 419 CSRF Token Mismatch
    $response->assertStatus(302); // Redirect after successful DELETE
    $this->assertDatabaseMissing('todo_items', [
        'id' => $todoId,
    ]);
});

test('POST requests do not require CSRF token in headers', function () {
    $data = [
        'title' => 'Test Without X-CSRF-Token Header',
    ];

    // Make request without X-CSRF-Token header
    $response = $this->post('/todos', $data, [
        'Accept' => 'application/json',
    ]);

    // Should succeed, not return 419
    expect($response->status())->not->toBe(419);
});

test('authenticated POST requests work without CSRF token', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $data = [
        'title' => 'Authenticated Todo Without CSRF',
    ];

    $response = $this->post('/todos', $data);

    // Should succeed for authenticated users without CSRF
    $response->assertStatus(302);
    $this->assertDatabaseHas('todo_items', [
        'title' => 'Authenticated Todo Without CSRF',
        'user_id' => $user->id,
    ]);
});

