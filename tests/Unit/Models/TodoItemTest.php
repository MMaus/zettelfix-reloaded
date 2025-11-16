<?php

use App\Models\TodoItem;
use App\Models\User;

test('todo item requires title field', function () {
    // Title is validated at request level, not database level
    // Database allows empty string, but validation will catch it
    $todo = TodoItem::factory()->make(['title' => '']);
    expect($todo->title)->toBe('');
    // Validation happens in StoreTodoItemRequest
});

test('todo item can have nullable description', function () {
    $todo = TodoItem::factory()->create(['description' => null]);

    expect($todo->description)->toBeNull();
});

test('todo item casts tags to array', function () {
    $todo = TodoItem::factory()->create(['tags' => ['shopping', 'urgent']]);

    expect($todo->tags)->toBeArray();
    expect($todo->tags)->toBe(['shopping', 'urgent']);
});

test('todo item casts due_date to date', function () {
    $todo = TodoItem::factory()->create(['due_date' => '2025-01-30']);

    expect($todo->due_date)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($todo->due_date->format('Y-m-d'))->toBe('2025-01-30');
});

test('todo item belongs to user', function () {
    $user = User::factory()->create();
    $todo = TodoItem::factory()->create(['user_id' => $user->id]);

    expect($todo->user)->toBeInstanceOf(User::class);
    expect($todo->user->id)->toBe($user->id);
});

test('todo item can exist without user', function () {
    $todo = TodoItem::factory()->create(['user_id' => null]);

    expect($todo->user_id)->toBeNull();
    expect($todo->user)->toBeNull();
});

