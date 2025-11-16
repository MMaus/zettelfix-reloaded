import type { TodoItem } from './todo';
import type { ShoppingListItem } from './shopping';

export interface SyncRequest {
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

export interface SyncResponse {
    todos: TodoItem[]; // All todos updated since last_synced_at
    shopping_items: ShoppingListItem[]; // All items updated since last_synced_at
    deleted: {
        todos: number[];
        shopping_items: number[];
    };
    synced_at: string; // Current server timestamp
}

export interface SyncStatus {
    lastSyncedAt: string | null; // ISO timestamp
    pendingChanges: number; // Count of local changes not yet synced
    isOnline: boolean;
    syncInProgress: boolean;
}

