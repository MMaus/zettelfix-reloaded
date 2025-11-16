<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { ShoppingHistoryItem } from '@/types/shopping';
import HistoryLibrary from '@/components/Shopping/HistoryLibrary.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

interface Props {
    historyItems: {
        data: ShoppingHistoryItem[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    filters?: {
        search?: string;
        categories?: string[];
        sort_by?: 'purchased_at' | 'name';
        sort_order?: 'asc' | 'desc';
    };
    allCategories: string[];
}

const props = defineProps<Props>();
const page = usePage();

// Filter state
const searchQuery = ref<string>(props.filters?.search || '');
const selectedCategories = ref<string[]>(props.filters?.categories || []);
const sortBy = ref<'purchased_at' | 'name'>(props.filters?.sort_by || 'purchased_at');
const sortOrder = ref<'asc' | 'desc'>(props.filters?.sort_order || 'desc');

const toggleCategory = (category: string) => {
    const index = selectedCategories.value.indexOf(category);
    if (index > -1) {
        selectedCategories.value.splice(index, 1);
    } else {
        selectedCategories.value.push(category);
    }
    
    applyFilters();
};

const applyFilters = () => {
    router.get('/shopping/history', {
        search: searchQuery.value || undefined,
        categories: selectedCategories.value.length > 0 ? selectedCategories.value : undefined,
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
    }, { preserveState: true, preserveScroll: true });
};

const toggleSort = () => {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    applyFilters();
};

const breadcrumbs = [
    { title: 'Shopping List', href: '/shopping' },
    { title: 'History', href: '/shopping/history' },
];
</script>

<template>
    <Head title="Shopping History" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 md:p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold md:text-3xl">Shopping History</h1>
                <Link href="/shopping">
                    <Button variant="outline">Back to Shopping List</Button>
                </Link>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <Input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search by item name..."
                    @input="applyFilters"
                    class="max-w-md"
                />
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
                    @change="applyFilters"
                    class="rounded border px-2 py-1 text-sm"
                >
                    <option value="purchased_at">Purchase Date</option>
                    <option value="name">Name</option>
                </select>
                <Button @click="toggleSort" variant="outline" size="sm">
                    {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </Button>
            </div>

            <!-- History Library -->
            <HistoryLibrary :items="historyItems.data" />

            <!-- Pagination -->
            <div v-if="historyItems.last_page > 1" class="mt-6 flex items-center justify-between">
                <div class="text-sm text-muted-foreground">
                    Showing {{ (historyItems.current_page - 1) * historyItems.per_page + 1 }} to
                    {{ Math.min(historyItems.current_page * historyItems.per_page, historyItems.total) }} of
                    {{ historyItems.total }} items
                </div>
                <div class="flex gap-2">
                    <Button
                        v-if="historyItems.current_page > 1"
                        @click="router.get(`/shopping/history?page=${historyItems.current_page - 1}`, { preserveState: true })"
                        variant="outline"
                        size="sm"
                    >
                        Previous
                    </Button>
                    <span class="text-sm text-muted-foreground">
                        Page {{ historyItems.current_page }} of {{ historyItems.last_page }}
                    </span>
                    <Button
                        v-if="historyItems.current_page < historyItems.last_page"
                        @click="router.get(`/shopping/history?page=${historyItems.current_page + 1}`, { preserveState: true })"
                        variant="outline"
                        size="sm"
                    >
                        Next
                    </Button>
                </div>
            </div>

            <div v-if="historyItems.data.length === 0" class="text-center text-muted-foreground py-8">
                No items in shopping history. Complete a checkout to add items to history.
            </div>
        </div>
    </AppLayout>
</template>

