import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import type { SyncRequest, SyncResponse, SyncStatus } from '@/types/sync';
import type { TodoItem } from '@/types/todo';
import type { ShoppingListItem } from '@/types/shopping';
import { useLocalStorage, getLocalStorageItem, setLocalStorageItem } from './useLocalStorage';
import { useOffline } from './useOffline';

const SYNC_INTERVAL = 30000; // 30 seconds
const SYNC_STORAGE_KEY = 'last_synced_at';
const PENDING_CHANGES_KEY = 'pending_sync_changes';

interface PendingChanges {
    todos: TodoItem[];
    shopping_items: ShoppingListItem[];
    deleted_ids: {
        todos: (number | string)[];
        shopping_items: (number | string)[];
    };
}

export function useSync() {
    const { isOnline } = useOffline();
    const lastSyncedAt = ref<string | null>(getLocalStorageItem<string | null>(SYNC_STORAGE_KEY, null));
    const syncInProgress = ref(false);
    const pendingChanges = ref<PendingChanges>(getLocalStorageItem<PendingChanges>(PENDING_CHANGES_KEY, {
        todos: [],
        shopping_items: [],
        deleted_ids: {
            todos: [],
            shopping_items: [],
        },
    }));

    let syncInterval: ReturnType<typeof setInterval> | null = null;

    const syncStatus = computed<SyncStatus>(() => ({
        lastSyncedAt: lastSyncedAt.value,
        pendingChanges: pendingChanges.value.todos.length + 
                       pendingChanges.value.shopping_items.length +
                       pendingChanges.value.deleted_ids.todos.length +
                       pendingChanges.value.deleted_ids.shopping_items.length,
        isOnline: isOnline.value,
        syncInProgress: syncInProgress.value,
    }));

    const addPendingTodo = (todo: TodoItem) => {
        // Remove if already exists (update case)
        pendingChanges.value.todos = pendingChanges.value.todos.filter(t => {
            if (typeof t.id === 'string' && typeof todo.id === 'string') {
                return t.id !== todo.id;
            }
            if (typeof t.id === 'number' && typeof todo.id === 'number') {
                return t.id !== todo.id;
            }
            return true;
        });
        pendingChanges.value.todos.push(todo);
        savePendingChanges();
    };

    const addPendingShoppingItem = (item: ShoppingListItem) => {
        // Remove if already exists (update case)
        pendingChanges.value.shopping_items = pendingChanges.value.shopping_items.filter(i => {
            if (typeof i.id === 'string' && typeof item.id === 'string') {
                return i.id !== item.id;
            }
            if (typeof i.id === 'number' && typeof item.id === 'number') {
                return i.id !== item.id;
            }
            return true;
        });
        pendingChanges.value.shopping_items.push(item);
        savePendingChanges();
    };

    const addPendingDeletion = (type: 'todos' | 'shopping_items', id: number | string) => {
        pendingChanges.value.deleted_ids[type].push(id);
        savePendingChanges();
    };

    const savePendingChanges = () => {
        setLocalStorageItem(PENDING_CHANGES_KEY, pendingChanges.value);
    };

    const clearPendingChanges = () => {
        pendingChanges.value = {
            todos: [],
            shopping_items: [],
            deleted_ids: {
                todos: [],
                shopping_items: [],
            },
        };
        savePendingChanges();
    };

    const performSync = async (): Promise<SyncResponse | null> => {
        if (!isOnline.value || syncInProgress.value) {
            return null;
        }

        syncInProgress.value = true;

        try {
            const syncRequest: SyncRequest = {
                last_synced_at: lastSyncedAt.value,
                local_changes: {
                    todos: pendingChanges.value.todos,
                    shopping_items: pendingChanges.value.shopping_items,
                    deleted_ids: pendingChanges.value.deleted_ids,
                },
            };

            const response = await fetch('/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(syncRequest),
            });

            if (!response.ok) {
                if (response.status === 401) {
                    // User not authenticated, stop syncing
                    stopSync();
                    return null;
                }
                throw new Error(`Sync failed: ${response.status}`);
            }

            const data: SyncResponse = await response.json();

            // Update last synced timestamp
            lastSyncedAt.value = data.synced_at;
            setLocalStorageItem(SYNC_STORAGE_KEY, data.synced_at);

            // Clear pending changes after successful sync
            clearPendingChanges();

            // Trigger Inertia reload to update UI with synced data
            router.reload({ only: ['todos', 'items'] });

            return data;
        } catch (error) {
            console.error('Sync error:', error);
            return null;
        } finally {
            syncInProgress.value = false;
        }
    };

    const startSync = () => {
        if (syncInterval) {
            return; // Already started
        }

        // Perform initial sync
        performSync();

        // Set up polling interval
        syncInterval = setInterval(() => {
            if (isOnline.value && pendingChanges.value.todos.length + 
                pendingChanges.value.shopping_items.length +
                pendingChanges.value.deleted_ids.todos.length +
                pendingChanges.value.deleted_ids.shopping_items.length > 0) {
                performSync();
            }
        }, SYNC_INTERVAL);
    };

    const stopSync = () => {
        if (syncInterval) {
            clearInterval(syncInterval);
            syncInterval = null;
        }
    };

    // Auto-start sync when online
    onMounted(() => {
        if (isOnline.value) {
            startSync();
        }
    });

    onUnmounted(() => {
        stopSync();
    });

    // Watch for online status changes
    const watchOnline = () => {
        if (isOnline.value && !syncInterval) {
            startSync();
        }
    };

    // Set up online/offline listeners
    if (typeof window !== 'undefined') {
        window.addEventListener('online', watchOnline);
        window.addEventListener('offline', stopSync);
    }

    return {
        syncStatus,
        performSync,
        startSync,
        stopSync,
        addPendingTodo,
        addPendingShoppingItem,
        addPendingDeletion,
        clearPendingChanges,
    };
}

