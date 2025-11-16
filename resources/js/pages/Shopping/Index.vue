<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import type { ShoppingListItem } from '@/types/shopping';
import ShoppingList from '@/components/Shopping/ShoppingList.vue';
import Basket from '@/components/Shopping/Basket.vue';
import { Button } from '@/components/ui/button';
import { useLocalStorage, setLocalStorageItem } from '@/composables/useLocalStorage';
import { useSync } from '@/composables/useSync';
import SyncIndicator from '@/components/Sync/SyncIndicator.vue';

interface Props {
    items: ShoppingListItem[];
    filters?: {
        categories?: string[];
        sort_by?: 'created_at' | 'name';
        sort_order?: 'asc' | 'desc';
    };
    basketCount: number;
    canCreate: boolean;
}

const props = defineProps<Props>();
const page = usePage();

// Sync functionality (only for authenticated users)
const { syncStatus } = useSync();
const isAuthenticated = computed(() => !!page.props.auth?.user);

// Checkout functionality
const checkoutForm = useForm({});

const checkout = () => {
    if (props.basketCount === 0) {
        alert('No items in basket to checkout.');
        return;
    }
    
    if (confirm(`Checkout ${props.basketCount} item(s)?`)) {
        checkoutForm.post('/shopping/checkout');
    }
};

// Local storage for offline support
const localItems = useLocalStorage<ShoppingListItem[]>('shopping_items', []);

// Sync server items to local storage
watch(() => props.items, (newItems) => {
    setLocalStorageItem('shopping_items', newItems);
}, { immediate: true, deep: true });

// Merge server items with local items (for offline support)
const allItems = computed(() => {
    const serverIds = new Set(props.items.map(i => i.id));
    const localOnly = localItems.value.filter(i => !serverIds.has(i.id));
    return [...props.items, ...localOnly];
});

// Filter state
const selectedCategories = ref<string[]>(props.filters?.categories || []);
const sortBy = ref<'created_at' | 'name'>(props.filters?.sort_by || 'created_at');
const sortOrder = ref<'asc' | 'desc'>(props.filters?.sort_order || 'desc');

// Separate basket items from regular items
const basketItems = computed(() => {
    return allItems.value.filter(item => item.in_basket);
});

// Filtered and sorted items (excluding basket items)
const filteredItems = computed(() => {
    let filtered = allItems.value.filter(item => !item.in_basket);

    // Filter by categories
    if (selectedCategories.value.length > 0) {
        filtered = filtered.filter(item => 
            item.categories?.some(cat => selectedCategories.value.includes(cat))
        );
    }

    // Sort
    filtered.sort((a, b) => {
        let aValue: string | number;
        let bValue: string | number;

        if (sortBy.value === 'name') {
            aValue = a.name.toLowerCase();
            bValue = b.name.toLowerCase();
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

// Get all unique categories from items
const allCategories = computed(() => {
    const categories = new Set<string>();
    allItems.value.forEach(item => {
        item.categories?.forEach(cat => categories.add(cat));
    });
    return Array.from(categories).sort();
});

const toggleCategory = (category: string) => {
    const index = selectedCategories.value.indexOf(category);
    if (index > -1) {
        selectedCategories.value.splice(index, 1);
    } else {
        selectedCategories.value.push(category);
    }
    
    // Update URL with filters
    router.get('/shopping', {
        categories: selectedCategories.value.length > 0 ? selectedCategories.value : undefined,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
    }, { preserveState: true, preserveScroll: true });
};

const toggleSort = () => {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    router.get('/shopping', {
        categories: selectedCategories.value.length > 0 ? selectedCategories.value : undefined,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
    }, { preserveState: true, preserveScroll: true });
};

const breadcrumbs = [
    { title: 'Shopping List', href: '/shopping' },
];
</script>

<template>
    <Head title="Shopping List" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 md:p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold md:text-3xl">My Shopping List</h1>
                    <SyncIndicator v-if="isAuthenticated" :status="syncStatus" class="mt-2" />
                </div>
                <div class="flex gap-2">
                    <Link v-if="isAuthenticated" href="/shopping/history">
                        <Button variant="outline">History</Button>
                    </Link>
                    <Link v-if="canCreate" href="/shopping/create">
                        <Button>Add Item</Button>
                    </Link>
                </div>
            </div>

            <!-- Basket section -->
            <Basket v-if="basketItems.length > 0" :items="basketItems" />

            <!-- Basket count indicator and checkout button -->
            <div v-if="basketCount > 0" class="mb-4 flex items-center justify-between rounded-lg bg-primary/10 p-3">
                <span class="text-sm font-medium">{{ basketCount }} item(s) in basket</span>
                <Button 
                    @click="checkout" 
                    :disabled="checkoutForm.processing"
                    size="sm"
                >
                    {{ checkoutForm.processing ? 'Processing...' : 'Checkout' }}
                </Button>
            </div>

            <!-- Filters -->
            <div class="mb-4 flex flex-wrap gap-2">
                <div v-if="allCategories.length > 0" class="flex flex-wrap gap-2">
                    <span class="text-sm font-medium">Filter by category:</span>
                    <button
                        v-for="category in allCategories"
                        :key="category"
                        @click="toggleCategory(category)"
                        :class="[
                            'rounded-full px-3 py-1 text-sm transition-colors',
                            selectedCategories.includes(category)
                                ? 'bg-primary text-primary-foreground'
                                : 'bg-muted hover:bg-muted/80'
                        ]"
                    >
                        {{ category }}
                    </button>
                </div>
            </div>

            <!-- Sort controls -->
            <div class="mb-4 flex items-center gap-4">
                <label class="text-sm font-medium">Sort by:</label>
                <select
                    v-model="sortBy"
                    @change="router.get('/shopping', { categories: selectedCategories.length > 0 ? selectedCategories : undefined, sort_by: sortBy, sort_order: sortOrder }, { preserveState: true })"
                    class="rounded border px-2 py-1 text-sm"
                >
                    <option value="created_at">Creation Date</option>
                    <option value="name">Name</option>
                </select>
                <Button @click="toggleSort" variant="outline" size="sm">
                    {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </Button>
            </div>

            <!-- Shopping List -->
            <ShoppingList :items="filteredItems" />

            <div v-if="filteredItems.length === 0" class="text-center text-muted-foreground py-8">
                No items in your shopping list. Add your first item to get started!
            </div>
        </div>
    </AppLayout>
</template>

