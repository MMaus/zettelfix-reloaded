# Quickstart Guide: Shopping and Todo List Synchronization

**Date**: 2025-01-27  
**Feature**: Shopping and Todo List Synchronization

## Overview

This feature adds synchronized todo lists and shopping lists to the application. Users can create and manage lists offline, and sync them across devices when authenticated.

## Key Concepts

1. **Offline-First**: Lists work completely offline using browser LocalStorage
2. **Optional Authentication**: Users can use lists without logging in (local only)
3. **Synchronization**: Logged-in users can sync lists across multiple devices
4. **Shopping Workflow**: Mark items as "in basket" during shopping, then checkout

## Architecture

- **Backend**: Laravel controllers return Inertia responses (no REST API)
- **Frontend**: Vue 3 components consume Inertia page props
- **Storage**: MySQL (server), LocalStorage (client offline)
- **Sync**: Polling-based synchronization with conflict resolution

## Getting Started

### 1. Database Setup

Run migrations to create the required tables:

```bash
php artisan migrate
```

This creates:
- `todo_items` table
- `shopping_list_items` table
- `shopping_history_items` table

### 2. Routes

Routes are defined in `routes/web.php`:

```php
// Todos
Route::get('/todos', [TodoItemController::class, 'index'])->name('todos.index');
Route::get('/todos/create', [TodoItemController::class, 'create'])->name('todos.create');
Route::post('/todos', [TodoItemController::class, 'store'])->name('todos.store');
Route::get('/todos/{id}/edit', [TodoItemController::class, 'edit'])->name('todos.edit');
Route::put('/todos/{id}', [TodoItemController::class, 'update'])->name('todos.update');
Route::delete('/todos/{id}', [TodoItemController::class, 'destroy'])->name('todos.destroy');

// Shopping Lists
Route::get('/shopping', [ShoppingListItemController::class, 'index'])->name('shopping.index');
Route::get('/shopping/create', [ShoppingListItemController::class, 'create'])->name('shopping.create');
Route::post('/shopping', [ShoppingListItemController::class, 'store'])->name('shopping.store');
Route::get('/shopping/{id}/edit', [ShoppingListItemController::class, 'edit'])->name('shopping.edit');
Route::put('/shopping/{id}', [ShoppingListItemController::class, 'update'])->name('shopping.update');
Route::delete('/shopping/{id}', [ShoppingListItemController::class, 'destroy'])->name('shopping.destroy');
Route::post('/shopping/checkout', [ShoppingListItemController::class, 'checkout'])->name('shopping.checkout');

// Shopping History
Route::get('/shopping/history', [ShoppingHistoryController::class, 'index'])->name('shopping.history');

// Synchronization (requires auth)
Route::post('/sync', [SyncController::class, 'sync'])->middleware('auth')->name('sync');
```

### 3. Models

#### TodoItem Model

```php
// app/Models/TodoItem.php
class TodoItem extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 
        'tags', 'due_date', 'synced_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'due_date' => 'date',
        'synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

#### ShoppingListItem Model

```php
// app/Models/ShoppingListItem.php
class ShoppingListItem extends Model
{
    protected $fillable = [
        'user_id', 'name', 'quantity', 
        'categories', 'in_basket', 'synced_at'
    ];

    protected $casts = [
        'categories' => 'array',
        'quantity' => 'decimal:2',
        'in_basket' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 4. Controllers

Controllers follow Laravel conventions and return Inertia responses:

```php
// app/Http/Controllers/TodoItemController.php
class TodoItemController extends Controller
{
    public function index(Request $request)
    {
        $todos = TodoItem::query()
            ->when($request->user(), fn($q) => $q->where('user_id', $request->user()->id))
            ->when(!$request->user(), fn($q) => $q->whereNull('user_id'))
            ->latest()
            ->get();

        return Inertia::render('Todos/Index', [
            'todos' => $todos,
            'canCreate' => true,
        ]);
    }

    // ... other methods
}
```

### 5. Frontend Components

#### Vue Page Component Example

```vue
<!-- resources/js/pages/Todos/Index.vue -->
<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { TodoItem } from '@/types/todo';

defineProps<{
  todos: TodoItem[];
  canCreate: boolean;
}>();

// Component logic
</script>

<template>
  <Head title="Todos" />
  
  <div class="container">
    <h1>My Todos</h1>
    
    <Link v-if="canCreate" href="/todos/create">
      Create Todo
    </Link>
    
    <TodoList :todos="todos" />
  </div>
</template>
```

### 6. TypeScript Types

Define types in `resources/js/types/todo.ts`:

```typescript
export interface TodoItem {
  id: number | string;
  user_id?: number | null;
  title: string;
  description?: string | null;
  tags?: string[];
  due_date?: string | null;
  created_at: string;
  updated_at: string;
  synced_at?: string | null;
}
```

### 7. Local Storage Composable

Create `resources/js/composables/useLocalStorage.ts`:

```typescript
import { ref, watch } from 'vue';

export function useLocalStorage<T>(key: string, defaultValue: T) {
  const stored = localStorage.getItem(key);
  const data = ref<T>(stored ? JSON.parse(stored) : defaultValue);

  watch(data, (newValue) => {
    localStorage.setItem(key, JSON.stringify(newValue));
  }, { deep: true });

  return data;
}
```

### 8. Synchronization Service

Create `app/Services/SyncService.php`:

```php
class SyncService
{
    public function sync(User $user, array $localChanges, ?string $lastSyncedAt): array
    {
        // Get server changes since last sync
        $serverTodos = $this->getServerChanges($user, 'todos', $lastSyncedAt);
        $serverShoppingItems = $this->getServerChanges($user, 'shopping_items', $lastSyncedAt);
        
        // Merge local changes
        $this->mergeLocalChanges($user, $localChanges);
        
        // Resolve conflicts (last write wins)
        $this->resolveConflicts($serverTodos, $localChanges['todos'] ?? []);
        
        return [
            'todos' => $serverTodos,
            'shopping_items' => $serverShoppingItems,
            'synced_at' => now()->toIso8601String(),
        ];
    }
    
    // ... implementation details
}
```

## Testing

### Feature Tests

```php
// tests/Feature/TodoItemTest.php
test('user can create todo item', function () {
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/todos', [
            'title' => 'Buy groceries',
            'description' => 'Milk, bread, eggs',
            'tags' => ['shopping', 'urgent'],
        ]);
    
    $response->assertRedirect('/todos');
    $this->assertDatabaseHas('todo_items', [
        'user_id' => $user->id,
        'title' => 'Buy groceries',
    ]);
});
```

### Unit Tests

```php
// tests/Unit/Services/SyncServiceTest.php
test('sync merges local and server changes', function () {
    $user = User::factory()->create();
    $service = new SyncService();
    
    $result = $service->sync($user, [
        'todos' => [/* local changes */],
    ], null);
    
    expect($result)->toHaveKey('todos');
    expect($result)->toHaveKey('synced_at');
});
```

## Common Tasks

### Adding a New Field to TodoItem

1. Create migration: `php artisan make:migration add_field_to_todo_items_table`
2. Update model `$fillable` array
3. Update TypeScript type definition
4. Update form components
5. Update validation rules

### Implementing Search

1. Add search input to page component
2. Add filter prop to controller
3. Update query with `where` clause
4. Pass filter to Inertia response

### Adding Pagination

1. Use Laravel's `paginate()` instead of `get()`
2. Update TypeScript types to include pagination meta
3. Add pagination component to Vue page

## Troubleshooting

### Items Not Syncing

1. Check `synced_at` timestamp in database
2. Verify user is authenticated (`auth` middleware)
3. Check browser console for sync errors
4. Verify `SyncService` is handling conflicts correctly

### Local Items Lost After Login

1. Check data merge logic in `SyncController`
2. Verify LocalStorage is being read on login
3. Check for duplicate detection logic (may be too strict)

### Performance Issues with Large Lists

1. Implement virtual scrolling (vue-virtual-scroller)
2. Add pagination to reduce initial load
3. Optimize database queries (add indexes)
4. Use eager loading to prevent N+1 queries

## Next Steps

1. Review [data-model.md](./data-model.md) for entity details
2. Review [contracts/inertia-props.md](./contracts/inertia-props.md) for API contracts
3. Review [research.md](./research.md) for technical decisions
4. Follow TDD: Write tests first, then implement
5. Use `/speckit.tasks` to generate task breakdown

## Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Vue 3 Documentation](https://vuejs.org/)
- [Constitution](../.specify/memory/constitution.md) - Project principles and standards

