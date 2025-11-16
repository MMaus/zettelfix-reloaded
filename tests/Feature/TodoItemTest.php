<?php

use App\Models\TodoItem;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('user can view todo items index page', function () {
    $response = $this->get('/todos');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Todos/Index')
        ->has('todos')
    );
});

test('user can create a todo item', function () {
    $data = [
        'title' => 'Buy groceries',
        'description' => 'Milk, bread, eggs',
        'tags' => ['shopping', 'urgent'],
        'due_date' => now()->addDays(7)->format('Y-m-d'), // Future date
    ];

    $response = $this->post('/todos', $data);

    $response->assertRedirect(route('todos.index'));
    $this->assertDatabaseHas('todo_items', [
        'title' => 'Buy groceries',
        'description' => 'Milk, bread, eggs',
    ]);

    $todo = TodoItem::where('title', 'Buy groceries')->first();
    expect($todo->tags)->toBe(['shopping', 'urgent']);
    expect($todo->due_date->format('Y-m-d'))->toBe($data['due_date']);
});

test('user can create todo item without authentication', function () {
    $data = [
        'title' => 'Test todo',
        'description' => 'Test description',
    ];

    $response = $this->post('/todos', $data);

    $response->assertRedirect('/todos');
    $this->assertDatabaseHas('todo_items', [
        'title' => 'Test todo',
        'user_id' => null,
    ]);
});

test('user can view edit page for todo item', function () {
    $todo = TodoItem::factory()->create();

    $response = $this->get("/todos/{$todo->id}/edit");

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Todos/Edit')
        ->has('todo')
        ->where('todo.id', $todo->id)
    );
});

test('user can update a todo item', function () {
    $todo = TodoItem::factory()->create([
        'title' => 'Original title',
        'description' => 'Original description',
    ]);

    $data = [
        'title' => 'Updated title',
        'description' => 'Updated description',
        'tags' => ['work'],
        'due_date' => now()->addDays(7)->format('Y-m-d'), // Future date
    ];

    $response = $this->put("/todos/{$todo->id}", $data);

    $response->assertRedirect(route('todos.index'));
    $this->assertDatabaseHas('todo_items', [
        'id' => $todo->id,
        'title' => 'Updated title',
        'description' => 'Updated description',
    ]);

    $todo->refresh();
    expect($todo->tags)->toBe(['work']);
    expect($todo->due_date->format('Y-m-d'))->toBe($data['due_date']);
});

test('user can delete a todo item', function () {
    $todo = TodoItem::factory()->create();

    $response = $this->delete("/todos/{$todo->id}");

    $response->assertRedirect(route('todos.index'));
    $this->assertDatabaseMissing('todo_items', [
        'id' => $todo->id,
    ]);
});

test('user can filter todos by tags', function () {
    TodoItem::factory()->create(['tags' => ['shopping', 'urgent']]);
    TodoItem::factory()->create(['tags' => ['work']]);
    TodoItem::factory()->create(['tags' => ['shopping']]);

    $response = $this->get('/todos?tags[]=shopping');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Todos/Index')
        ->has('todos', 2) // Should have 2 todos with 'shopping' tag
    );
});

test('authenticated user sees only their todos', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    TodoItem::factory()->create(['user_id' => $user->id, 'title' => 'My todo']);
    TodoItem::factory()->create(['user_id' => $otherUser->id, 'title' => 'Other todo']);
    TodoItem::factory()->create(['user_id' => null, 'title' => 'Public todo']);

    $response = $this->actingAs($user)->get('/todos');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Todos/Index')
        ->has('todos', 1) // Should only see user's todos
        ->where('todos.0.title', 'My todo')
    );
});

test('unauthenticated user sees todos without user_id', function () {
    TodoItem::factory()->create(['user_id' => null, 'title' => 'Public todo']);
    TodoItem::factory()->create(['user_id' => User::factory()->create()->id, 'title' => 'Private todo']);

    $response = $this->get('/todos');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Todos/Index')
        ->has('todos', 1) // Should only see public todos
        ->where('todos.0.title', 'Public todo')
    );
});
