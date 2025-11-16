# Inertia.js Page Props Contracts

**Date**: 2025-01-27  
**Feature**: Shopping and Todo List Synchronization

## Overview

Since this application uses Inertia.js, there are no REST API endpoints. Instead, controllers return Inertia responses with page props. This document defines the structure of page props for each page/route.

## Page Contracts

### Todos Index Page

**Route**: `GET /todos`  
**Controller**: `TodoItemController@index`  
**Page Component**: `resources/js/pages/Todos/Index.vue`

**Props**:
```typescript
{
  todos: TodoItem[];
  filters?: {
    tags?: string[];
    due_date_from?: string;
    due_date_to?: string;
    sort_by?: 'created_at' | 'due_date';
    sort_order?: 'asc' | 'desc';
  };
  canCreate: boolean; // Always true for authenticated users
}
```

**Example**:
```php
return Inertia::render('Todos/Index', [
    'todos' => $todos,
    'filters' => $filters,
    'canCreate' => true,
]);
```

---

### Todo Create Page

**Route**: `GET /todos/create`  
**Controller**: `TodoItemController@create`  
**Page Component**: `resources/js/pages/Todos/Create.vue`

**Props**:
```typescript
{
  // No props needed for create form
}
```

---

### Todo Edit Page

**Route**: `GET /todos/{id}/edit`  
**Controller**: `TodoItemController@edit`  
**Page Component**: `resources/js/pages/Todos/Edit.vue`

**Props**:
```typescript
{
  todo: TodoItem;
  canUpdate: boolean; // From policy
  canDelete: boolean; // From policy
}
```

---

### Shopping List Index Page

**Route**: `GET /shopping`  
**Controller**: `ShoppingListItemController@index`  
**Page Component**: `resources/js/pages/Shopping/Index.vue`

**Props**:
```typescript
{
  items: ShoppingListItem[];
  filters?: {
    categories?: string[];
    in_basket?: boolean;
    sort_by?: 'created_at' | 'name';
    sort_order?: 'asc' | 'desc';
  };
  basketCount: number; // Count of items marked "in basket"
  canCreate: boolean;
}
```

---

### Shopping List Create Page

**Route**: `GET /shopping/create`  
**Controller**: `ShoppingListItemController@create`  
**Page Component**: `resources/js/pages/Shopping/Create.vue`

**Props**:
```typescript
{
  // No props needed for create form
  // Can optionally include recent categories for autocomplete
  recentCategories?: string[];
}
```

---

### Shopping History Page

**Route**: `GET /shopping/history`  
**Controller**: `ShoppingHistoryController@index`  
**Page Component**: `resources/js/pages/Shopping/History.vue`

**Props**:
```typescript
{
  history: ShoppingHistoryItem[];
  pagination: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters?: {
    categories?: string[];
    search?: string; // Search by name
    sort_by?: 'purchased_at' | 'name';
    sort_order?: 'asc' | 'desc';
  };
}
```

---

### Sync Status Page (Optional)

**Route**: `GET /sync/status`  
**Controller**: `SyncController@status`  
**Page Component**: `resources/js/pages/Sync/Status.vue`

**Props**:
```typescript
{
  lastSyncedAt: string | null; // ISO timestamp
  pendingChanges: number; // Count of local changes not yet synced
  isOnline: boolean;
  syncInProgress: boolean;
}
```

---

## Form Request Contracts

### Store Todo Item Request

**Route**: `POST /todos`  
**Controller**: `TodoItemController@store`  
**Request Class**: `StoreTodoItemRequest`

**Validation Rules**:
```php
[
    'title' => 'required|string|max:255',
    'description' => 'nullable|string|max:65535',
    'tags' => 'nullable|array',
    'tags.*' => 'string|max:50',
    'due_date' => 'nullable|date|after_or_equal:today',
]
```

**Response**: Redirects to `Todos/Index` with success message

---

### Update Todo Item Request

**Route**: `PUT /todos/{id}`  
**Controller**: `TodoItemController@update`  
**Request Class**: `UpdateTodoItemRequest`

**Validation Rules**: Same as StoreTodoItemRequest

**Response**: Redirects to `Todos/Index` with success message

---

### Store Shopping List Item Request

**Route**: `POST /shopping`  
**Controller**: `ShoppingListItemController@store`  
**Request Class**: `StoreShoppingListItemRequest`

**Validation Rules**:
```php
[
    'name' => 'required|string|max:255',
    'quantity' => 'required|numeric|min:0.01|max:999999.99',
    'categories' => 'nullable|array',
    'categories.*' => 'string|max:50',
]
```

**Response**: Redirects to `Shopping/Index` with success message

---

### Update Shopping List Item Request

**Route**: `PUT /shopping/{id}`  
**Controller**: `ShoppingListItemController@update`  
**Request Class**: `UpdateShoppingListItemRequest`

**Validation Rules**: Same as StoreShoppingListItemRequest

**Response**: Redirects to `Shopping/Index` with success message

---

## Synchronization Endpoint

### Sync Data

**Route**: `POST /sync`  
**Controller**: `SyncController@sync`  
**Authentication**: Required (auth middleware)

**Request Body**:
```typescript
{
  last_synced_at: string | null; // ISO timestamp
  local_changes: {
    todos: TodoItem[]; // Items with updated_at > synced_at
    shopping_items: ShoppingListItem[]; // Items with updated_at > synced_at
    deleted_ids: {
      todos: (number | string)[];
      shopping_items: (number | string)[];
    };
  };
}
```

**Response**:
```typescript
{
  todos: TodoItem[]; // All todos updated since last_synced_at
  shopping_items: ShoppingListItem[]; // All items updated since last_synced_at
  deleted: {
    todos: number[];
    shopping_items: number[];
  };
  synced_at: string; // Current server timestamp
}
```

**Status Codes**:
- `200 OK`: Sync successful
- `401 Unauthorized`: Not authenticated
- `422 Unprocessable Entity`: Validation errors

---

## Local Storage Contracts

### Local Storage Keys

- `todos`: Array of TodoItem (local items when not logged in)
- `shopping_items`: Array of ShoppingListItem (local items when not logged in)
- `last_synced_at`: ISO timestamp string
- `basket_state`: Object mapping item IDs to boolean (in_basket state)

### Local Storage Structure

```typescript
interface LocalStorageData {
  todos: TodoItem[];
  shopping_items: ShoppingListItem[];
  last_synced_at: string | null;
  basket_state: Record<string | number, boolean>;
}
```

---

## Error Response Format

All Inertia error responses follow Laravel's standard validation error format:

```typescript
{
  message: string; // General error message
  errors: {
    [field: string]: string[]; // Field-specific errors
  };
}
```

Example:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "quantity": ["The quantity must be at least 0.01."]
  }
}
```

---

## Shared Props (Available on All Pages)

These props are automatically shared via Inertia's `HandleInertiaRequests` middleware:

```typescript
{
  auth: {
    user: {
      id: number;
      email: string;
    } | null;
  };
  flash: {
    success?: string;
    error?: string;
    message?: string;
  };
}
```

---

## Notes

- All timestamps are ISO 8601 format strings
- All numeric IDs are integers (server) or strings (local UUIDs)
- Arrays are always present (empty array if no items)
- Optional props use TypeScript `?` notation
- Pagination follows Laravel's standard pagination format
- Error handling uses Inertia's built-in error handling

