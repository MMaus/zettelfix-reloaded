<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import type { TodoItem } from '@/types/todo';
import TodoList from '@/components/Todo/TodoList.vue';
import { Button } from '@/components/ui/button';
import { useLocalStorage, getLocalStorageItem, setLocalStorageItem } from '@/composables/useLocalStorage';
import { useSync } from '@/composables/useSync';
import SyncIndicator from '@/components/Sync/SyncIndicator.vue';

interface Props {
    todos: TodoItem[];
    filters?: {
        tags?: string[];
        sort_by?: 'created_at' | 'due_date';
        sort_order?: 'asc' | 'desc';
    };
    canCreate: boolean;
}

const props = defineProps<Props>();
const page = usePage();

// Sync functionality (only for authenticated users)
const { syncStatus } = useSync();
const isAuthenticated = computed(() => !!page.props.auth?.user);

// Local storage for offline support
const localTodos = useLocalStorage<TodoItem[]>('todos', []);

// Sync server todos to local storage
watch(() => props.todos, (newTodos) => {
    setLocalStorageItem('todos', newTodos);
}, { immediate: true, deep: true });

// Merge server todos with local todos (for offline support)
const allTodos = computed(() => {
    const serverIds = new Set(props.todos.map(t => t.id));
    const localOnly = localTodos.value.filter(t => !serverIds.has(t.id));
    return [...props.todos, ...localOnly];
});

// Filter state
const selectedTags = ref<string[]>(props.filters?.tags || []);
const sortBy = ref<'created_at' | 'due_date'>(props.filters?.sort_by || 'created_at');
const sortOrder = ref<'asc' | 'desc'>(props.filters?.sort_order || 'desc');

// Filtered and sorted todos
const filteredTodos = computed(() => {
    let filtered = [...allTodos.value];

    // Filter by tags
    if (selectedTags.value.length > 0) {
        filtered = filtered.filter(todo => 
            todo.tags?.some(tag => selectedTags.value.includes(tag))
        );
    }

    // Sort
    filtered.sort((a, b) => {
        let aValue: string | number;
        let bValue: string | number;

        if (sortBy.value === 'due_date') {
            aValue = a.due_date || '';
            bValue = b.due_date || '';
        } else {
            aValue = a.created_at;
            bValue = b.created_at;
        }

        if (sortOrder.value === 'asc') {
            return aValue > bValue ? 1 : -1;
        } else {
            return aValue < bValue ? 1 : -1;
        }
    });

    return filtered;
});

// Get all unique tags from todos
const allTags = computed(() => {
    const tags = new Set<string>();
    allTodos.value.forEach(todo => {
        todo.tags?.forEach(tag => tags.add(tag));
    });
    return Array.from(tags).sort();
});

const toggleTag = (tag: string) => {
    const index = selectedTags.value.indexOf(tag);
    if (index > -1) {
        selectedTags.value.splice(index, 1);
    } else {
        selectedTags.value.push(tag);
    }
    
    // Update URL with filters
    router.get('/todos', {
        tags: selectedTags.value.length > 0 ? selectedTags.value : undefined,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
    }, { preserveState: true, preserveScroll: true });
};

const toggleSort = () => {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    router.get('/todos', {
        tags: selectedTags.value.length > 0 ? selectedTags.value : undefined,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
    }, { preserveState: true, preserveScroll: true });
};

const breadcrumbs = [
    { title: 'Todos', href: '/todos' },
];
</script>

<template>
    <Head title="Todos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 md:p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold md:text-3xl">My Todos</h1>
                    <SyncIndicator v-if="isAuthenticated" :status="syncStatus" class="mt-2" />
                </div>
                <Link v-if="canCreate" href="/todos/create">
                    <Button>Add Todo</Button>
                </Link>
            </div>

            <!-- Filters -->
            <div class="mb-4 flex flex-wrap gap-2">
                <div v-if="allTags.length > 0" class="flex flex-wrap gap-2">
                    <span class="text-sm font-medium">Filter by tags:</span>
                    <button
                        v-for="tag in allTags"
                        :key="tag"
                        @click="toggleTag(tag)"
                        :class="[
                            'rounded-full px-3 py-1 text-sm transition-colors',
                            selectedTags.includes(tag)
                                ? 'bg-primary text-primary-foreground'
                                : 'bg-muted hover:bg-muted/80'
                        ]"
                    >
                        {{ tag }}
                    </button>
                </div>
            </div>

            <!-- Sort controls -->
            <div class="mb-4 flex items-center gap-4">
                <label class="text-sm font-medium">Sort by:</label>
                <select
                    v-model="sortBy"
                    @change="router.get('/todos', { tags: selectedTags.length > 0 ? selectedTags : undefined, sort_by: sortBy, sort_order: sortOrder }, { preserveState: true })"
                    class="rounded border px-2 py-1 text-sm"
                >
                    <option value="created_at">Creation Date</option>
                    <option value="due_date">Due Date</option>
                </select>
                <Button @click="toggleSort" variant="outline" size="sm">
                    {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </Button>
            </div>

            <!-- Todo List -->
            <TodoList :todos="filteredTodos" />

            <div v-if="filteredTodos.length === 0" class="text-center text-muted-foreground py-8">
                No todos found. Create your first todo to get started!
            </div>
        </div>
    </AppLayout>
</template>

