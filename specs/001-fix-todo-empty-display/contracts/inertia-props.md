# Inertia.js Props Contract: Todo Empty State Fix

**Date**: 2025-01-27  
**Feature**: Fix Todo Empty State Display  
**Type**: Frontend-only fix (no API changes)

## Overview

This feature does not modify any backend API endpoints or Inertia.js prop structures. The existing contract remains unchanged. This document describes the current contract for reference.

## Page: Todos/Index

**Route**: `GET /todos`  
**Controller**: `App\Http\Controllers\TodoItemController@index`  
**Component**: `resources/js/pages/Todos/Index.vue`

### Props Structure

```typescript
interface Props {
    todos: TodoItem[];
    filters?: {
        tags?: string[];
        sort_by?: 'created_at' | 'due_date';
        sort_order?: 'asc' | 'desc';
    };
    canCreate: boolean;
}
```

### Props Description

#### `todos: TodoItem[]`

**Type**: Array of TodoItem objects  
**Required**: Yes  
**Source**: Database query filtered by user and optional tag filters, sorted by specified field

**TodoItem Structure**:
```typescript
interface TodoItem {
    id: number | string;
    user_id?: number | null;
    title: string;
    description?: string | null;
    tags?: string[];
    due_date?: string | null;  // ISO date string
    created_at: string;         // ISO timestamp
    updated_at: string;         // ISO timestamp
    synced_at?: string | null;  // ISO timestamp
}
```

**Behavior**:
- For authenticated users: Returns todos where `user_id` matches authenticated user
- For unauthenticated users: Returns todos where `user_id` is null (public todos)
- Can be empty array `[]` when user has no todos

#### `filters?: {...}`

**Type**: Optional object  
**Required**: No  
**Source**: URL query parameters

**Fields**:
- `tags?: string[]` - Array of tag strings to filter by (AND logic - todo must have all tags)
- `sort_by?: 'created_at' | 'due_date'` - Field to sort by (default: 'created_at')
- `sort_order?: 'asc' | 'desc'` - Sort direction (default: 'desc')

**Example**:
```typescript
{
    tags: ['work', 'urgent'],
    sort_by: 'due_date',
    sort_order: 'asc'
}
```

#### `canCreate: boolean`

**Type**: Boolean  
**Required**: Yes  
**Source**: Authorization check (typically always `true` for this endpoint)

**Purpose**: Controls whether "Add Todo" button is displayed

## No Contract Changes

This feature fix does not require changes to:
- Backend controller response structure
- Inertia.js prop types
- Route definitions
- Request/response formats

## Frontend-Only Changes

The fix involves:
1. Adding missing `localTodos` variable definition (frontend state)
2. Ensuring proper empty state rendering (frontend display logic)
3. Updating button label text (frontend UI)
4. Aligning visual styling (frontend CSS classes)

All changes are contained within Vue components and do not affect the Inertia.js contract.

