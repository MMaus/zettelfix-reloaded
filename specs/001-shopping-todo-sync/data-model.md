# Data Model: Shopping and Todo List Synchronization

**Date**: 2025-01-27  
**Feature**: Shopping and Todo List Synchronization

## Entity Relationship Overview

```
User (1) ──< (0..*) TodoItem
User (1) ──< (0..*) ShoppingListItem
User (1) ──< (0..*) ShoppingHistoryItem
```

## Entities

### User

**Description**: Represents an authenticated user account. Extends Laravel's default User model.

**Database Table**: `users` (existing table, may need additional columns)

**Fields**:
- `id` (bigint, primary key, auto-increment)
- `email` (string, unique, required)
- `email_verified_at` (timestamp, nullable)
- `password` (string, hashed, required)
- `remember_token` (string, nullable) - For permanent login
- `created_at` (timestamp)
- `updated_at` (timestamp)

**Relationships**:
- `hasMany(TodoItem::class)`
- `hasMany(ShoppingListItem::class)`
- `hasMany(ShoppingHistoryItem::class)`

**Validation Rules**:
- Email: required, email format, unique
- Password: required, min 8 characters (Laravel Fortify default)

**Notes**:
- Uses Laravel Fortify for authentication
- Permanent login via `remember_token`
- User can exist without any items (empty lists)

---

### TodoItem

**Description**: Represents a single todo task. Can belong to a user (if authenticated) or be stored locally (if not authenticated).

**Database Table**: `todo_items`

**Fields**:
- `id` (bigint, primary key, auto-increment)
- `user_id` (bigint, foreign key, nullable) - NULL if not logged in (local only)
- `title` (string, required, max 255)
- `description` (text, nullable)
- `tags` (json, nullable) - Array of tag strings
- `due_date` (date, nullable)
- `created_at` (timestamp, required)
- `updated_at` (timestamp, required)
- `synced_at` (timestamp, nullable) - Last successful sync timestamp

**Indexes**:
- `user_id` (index for user queries)
- `user_id, created_at` (composite index for sorting)
- `user_id, due_date` (composite index for due date filtering)

**Relationships**:
- `belongsTo(User::class)` - nullable

**Validation Rules**:
- `title`: required, string, max 255 characters
- `description`: nullable, string, max 65535 characters
- `tags`: nullable, array, each tag max 50 characters
- `due_date`: nullable, date format (Y-m-d)
- `user_id`: nullable, exists in users table if provided

**State Transitions**:
- Created → Can be edited or deleted
- No completion status in MVP (can be added later)

**Local Storage Format** (when user not logged in):
```json
{
  "id": "local-uuid",
  "title": "Buy groceries",
  "description": "Milk, bread, eggs",
  "tags": ["shopping", "urgent"],
  "due_date": "2025-01-30",
  "created_at": "2025-01-27T10:00:00Z",
  "updated_at": "2025-01-27T10:00:00Z"
}
```

**Notes**:
- `user_id` is nullable to support offline mode
- `synced_at` tracks last successful synchronization
- Tags stored as JSON array in database
- Local items use UUIDs instead of database IDs

---

### ShoppingListItem

**Description**: Represents a single item to be purchased. Can belong to a user (if authenticated) or be stored locally.

**Database Table**: `shopping_list_items`

**Fields**:
- `id` (bigint, primary key, auto-increment)
- `user_id` (bigint, foreign key, nullable) - NULL if not logged in
- `name` (string, required, max 255)
- `quantity` (decimal(8,2), required, default 1.0)
- `categories` (json, nullable) - Array of category strings
- `in_basket` (boolean, default false) - Temporary state for shopping workflow
- `created_at` (timestamp, required)
- `updated_at` (timestamp, required)
- `synced_at` (timestamp, nullable) - Last successful sync timestamp

**Indexes**:
- `user_id` (index for user queries)
- `user_id, created_at` (composite index for sorting)
- `user_id, in_basket` (composite index for basket filtering)

**Relationships**:
- `belongsTo(User::class)` - nullable

**Validation Rules**:
- `name`: required, string, max 255 characters
- `quantity`: required, numeric, min 0.01, max 999999.99
- `categories`: nullable, array, each category max 50 characters
- `in_basket`: boolean, default false
- `user_id`: nullable, exists in users table if provided

**State Transitions**:
- Created → Can be edited, deleted, or marked "in basket"
- "in basket" → Can be unmarked or checked out
- Checkout → Moved to ShoppingHistoryItem, removed from list

**Local Storage Format** (when user not logged in):
```json
{
  "id": "local-uuid",
  "name": "Milk",
  "quantity": 2,
  "categories": ["Dairy", "Beverages"],
  "in_basket": false,
  "created_at": "2025-01-27T10:00:00Z",
  "updated_at": "2025-01-27T10:00:00Z"
}
```

**Notes**:
- `in_basket` is temporary state, not synced across devices
- Quantity supports decimals (e.g., 1.5 kg)
- Categories stored as JSON array
- On checkout, items moved to history and removed from this table

---

### ShoppingHistoryItem

**Description**: Represents a previously purchased item. Used for quick re-adding to shopping list.

**Database Table**: `shopping_history_items`

**Fields**:
- `id` (bigint, primary key, auto-increment)
- `user_id` (bigint, foreign key, required) - Always belongs to authenticated user
- `name` (string, required, max 255)
- `quantity` (decimal(8,2), required)
- `categories` (json, nullable) - Array of category strings
- `purchased_at` (timestamp, required) - When item was checked out
- `created_at` (timestamp, required)
- `updated_at` (timestamp, required)

**Indexes**:
- `user_id` (index for user queries)
- `user_id, purchased_at` (composite index for sorting by purchase date)
- `user_id, name` (composite index for search)

**Relationships**:
- `belongsTo(User::class)` - required

**Validation Rules**:
- `name`: required, string, max 255 characters
- `quantity`: required, numeric, min 0.01, max 999999.99
- `categories`: nullable, array, each category max 50 characters
- `purchased_at`: required, timestamp
- `user_id`: required, exists in users table

**Notes**:
- Only created for authenticated users (no local-only history)
- Created when items are checked out from ShoppingListItem
- Used for browsing and re-adding to shopping list
- Can be searched and filtered by category
- Retained indefinitely (no automatic deletion)

---

## Data Flow

### Creating Items (Offline)

1. User creates item → Stored in browser LocalStorage
2. Item has local UUID, no `user_id`
3. On login → Items sent to server for merge
4. Server creates items with `user_id`, returns server IDs
5. Client updates local storage with server IDs

### Creating Items (Online, Authenticated)

1. User creates item → Sent to server via Inertia form
2. Server creates item with `user_id`
3. Server returns item with server ID
4. Client updates local state

### Synchronization Flow

1. Client sends sync request with `last_synced_at` timestamp
2. Server queries items where `updated_at > last_synced_at` OR `created_at > last_synced_at`
3. Server also receives client's local changes (items with `updated_at` > `synced_at`)
4. Server merges changes, resolves conflicts (last write wins)
5. Server returns all changed items
6. Client updates local storage and component state
7. Client updates `synced_at` timestamp

### Checkout Flow

1. User marks items as "in basket" (local state only)
2. User clicks checkout
3. For each "in basket" item:
   - Create ShoppingHistoryItem with same name, quantity, categories
   - Set `purchased_at` to current timestamp
   - Delete ShoppingListItem
4. Sync changes to server (if authenticated)
5. Update local storage

### Data Merge on Login

1. User logs in with local items in storage
2. Client sends local items to server
3. Server checks for duplicates:
   - Match on `name`/`title` (case-insensitive)
   - Match on `created_at` within 1-hour window
4. Non-duplicates are created on server
5. Server returns merged list
6. Client updates local storage with server IDs

---

## Database Migrations

### Migration: create_todo_items_table

```php
Schema::create('todo_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('description')->nullable();
    $table->json('tags')->nullable();
    $table->date('due_date')->nullable();
    $table->timestamp('synced_at')->nullable();
    $table->timestamps();
    
    $table->index('user_id');
    $table->index(['user_id', 'created_at']);
    $table->index(['user_id', 'due_date']);
});
```

### Migration: create_shopping_list_items_table

```php
Schema::create('shopping_list_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    $table->string('name');
    $table->decimal('quantity', 8, 2)->default(1.0);
    $table->json('categories')->nullable();
    $table->boolean('in_basket')->default(false);
    $table->timestamp('synced_at')->nullable();
    $table->timestamps();
    
    $table->index('user_id');
    $table->index(['user_id', 'created_at']);
    $table->index(['user_id', 'in_basket']);
});
```

### Migration: create_shopping_history_items_table

```php
Schema::create('shopping_history_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->decimal('quantity', 8, 2);
    $table->json('categories')->nullable();
    $table->timestamp('purchased_at');
    $table->timestamps();
    
    $table->index('user_id');
    $table->index(['user_id', 'purchased_at']);
    $table->index(['user_id', 'name']);
});
```

---

## TypeScript Types

### TodoItem Type

```typescript
interface TodoItem {
  id: number | string; // number for server, string (UUID) for local
  user_id?: number | null;
  title: string;
  description?: string | null;
  tags?: string[];
  due_date?: string | null; // ISO date string
  created_at: string; // ISO timestamp
  updated_at: string; // ISO timestamp
  synced_at?: string | null; // ISO timestamp
}
```

### ShoppingListItem Type

```typescript
interface ShoppingListItem {
  id: number | string; // number for server, string (UUID) for local
  user_id?: number | null;
  name: string;
  quantity: number;
  categories?: string[];
  in_basket: boolean;
  created_at: string; // ISO timestamp
  updated_at: string; // ISO timestamp
  synced_at?: string | null; // ISO timestamp
}
```

### ShoppingHistoryItem Type

```typescript
interface ShoppingHistoryItem {
  id: number;
  user_id: number;
  name: string;
  quantity: number;
  categories?: string[];
  purchased_at: string; // ISO timestamp
  created_at: string; // ISO timestamp
  updated_at: string; // ISO timestamp
}
```

---

## Validation Summary

| Entity | Required Fields | Optional Fields | Constraints |
|--------|----------------|----------------|-------------|
| TodoItem | title, created_at | description, tags, due_date, user_id | title max 255, tags array |
| ShoppingListItem | name, quantity, created_at | categories, user_id, in_basket | name max 255, quantity 0.01-999999.99 |
| ShoppingHistoryItem | name, quantity, purchased_at, user_id | categories | name max 255, quantity 0.01-999999.99 |

