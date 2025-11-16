# Data Model: Fix Todo Empty State Display

**Date**: 2025-01-27  
**Feature**: Fix Todo Empty State Display  
**Phase**: 1 - Design

## Overview

This feature does not introduce new data entities or modify existing data models. It fixes a frontend rendering bug and improves visual consistency. The existing `TodoItem` entity remains unchanged.

## Existing Entities

### TodoItem

**Purpose**: Represents a single todo task in the system.

**Attributes**:
- `id`: Unique identifier (string/number)
- `title`: Task title (string)
- `description`: Task description (string, optional)
- `due_date`: Due date (string/Date, optional)
- `tags`: Array of tag strings (string[], optional)
- `completed`: Completion status (boolean)
- `created_at`: Creation timestamp (string)
- `updated_at`: Last update timestamp (string)
- `user_id`: Owner user ID (number, optional - null for public todos)

**Relationships**:
- Belongs to User (optional - can be null for public todos)
- No direct relationships to other entities

**Validation Rules**:
- Title is required (enforced by backend)
- Tags are optional array of strings
- Due date must be valid date format if provided

**State Transitions**:
- Created → In Progress (default state)
- In Progress → Completed (via completion toggle)
- Completed → In Progress (via completion toggle)

## No Data Model Changes

This feature does not require:
- New database tables
- New columns on existing tables
- New relationships
- Data migrations
- Model modifications

## Frontend State Management

### Local Storage

**Entity**: `TodoItem[]` stored as `'todos'` key in localStorage

**Purpose**: Provides offline support by caching server todos locally and merging with server data on page load.

**Structure**: Array of `TodoItem` objects matching server response format.

**Operations**:
- Read: `getLocalStorageItem('todos')` - retrieves cached todos
- Write: `setLocalStorageItem('todos', todos)` - updates cache when server todos change
- Merge: Server todos + local-only todos (todos not present in server response)

**Note**: The bug fix adds the missing `localTodos` variable that reads from localStorage using `useLocalStorage<TodoItem[]>('todos', [])`.

## Component Props

### Todos/Index.vue Props

**Interface**: `Props`

```typescript
interface Props {
    todos: TodoItem[];              // Server-provided todos
    filters?: {
        tags?: string[];
        sort_by?: 'created_at' | 'due_date';
        sort_order?: 'asc' | 'desc';
    };
    canCreate: boolean;              // Permission to create todos
}
```

**Source**: Inertia.js page props from `TodoItemController@index`

### TodoList.vue Props

**Interface**: `Props`

```typescript
interface Props {
    todos: TodoItem[];               // Filtered and sorted todos to display
}
```

**Source**: Computed property `filteredTodos` from parent `Index.vue`

## Computed State

### allTodos

**Type**: `ComputedRef<TodoItem[]>`

**Purpose**: Merges server todos with local-only todos for offline support.

**Logic**:
1. Extract server todo IDs into a Set
2. Filter local todos to find items not in server response
3. Combine server todos + local-only todos

**Bug Fix**: Requires `localTodos` to be defined using `useLocalStorage<TodoItem[]>('todos', [])`.

### filteredTodos

**Type**: `ComputedRef<TodoItem[]>`

**Purpose**: Applies tag filters and sorting to `allTodos`.

**Logic**:
1. Start with `allTodos`
2. Filter by selected tags (if any tags selected)
3. Sort by `sort_by` field in `sort_order` direction
4. Return filtered and sorted array

## Empty State Handling

**Condition**: `filteredTodos.length === 0`

**Display**: Empty state message shown in parent component (`Index.vue`), not in `TodoList.vue`.

**Message**: "No todos found. Create your first todo to get started!"

**Styling**: `text-center text-muted-foreground py-8` (matches Shopping List empty state)

