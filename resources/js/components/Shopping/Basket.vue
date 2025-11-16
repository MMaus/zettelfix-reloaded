<script setup lang="ts">
import type { ShoppingListItem } from '@/types/shopping';
import ShoppingItem from './ShoppingItem.vue';

interface Props {
    items: ShoppingListItem[];
}

defineProps<Props>();
</script>

<template>
    <div v-if="items.length > 0" class="mb-6">
        <h2 class="mb-4 text-xl font-semibold">Items in Basket</h2>
        <div class="space-y-2">
            <ShoppingItem
                v-for="item in items"
                :key="item.id"
                :item="item"
            />
        </div>
    </div>
</template>

