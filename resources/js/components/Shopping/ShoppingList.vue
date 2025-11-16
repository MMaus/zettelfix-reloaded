<script setup lang="ts">
import { computed, ref, toRefs } from 'vue';
import type { ShoppingListItem } from '@/types/shopping';
import ShoppingItem from './ShoppingItem.vue';

interface Props {
    items: ShoppingListItem[];
}

const props = defineProps<Props>();
const { items } = toRefs(props);

const ITEM_HEIGHT = 72; // Approximate row height (px), including spacing
const VIEWPORT_HEIGHT = 480; // Max list height before scrolling (px)
const OVERSCAN = 5; // Extra items rendered above/below the viewport

const containerRef = ref<HTMLElement | null>(null);
const scrollTop = ref(0);

const isVirtualized = computed(() => items.value.length > 100);

const totalHeight = computed(() => items.value.length * ITEM_HEIGHT);

const startIndex = computed(() => {
    if (!isVirtualized.value) {
        return 0;
    }

    return Math.max(0, Math.floor(scrollTop.value / ITEM_HEIGHT) - OVERSCAN);
});

const endIndex = computed(() => {
    if (!isVirtualized.value) {
        return items.value.length;
    }

    const visibleCount = Math.ceil(VIEWPORT_HEIGHT / ITEM_HEIGHT) + OVERSCAN * 2;

    return Math.min(items.value.length, startIndex.value + visibleCount);
});

const visibleItems = computed(() =>
    items.value.slice(startIndex.value, endIndex.value).map((item, index) => ({
        item,
        index: startIndex.value + index,
    })),
);

const onScroll = () => {
    if (!containerRef.value) {
        return;
    }

    scrollTop.value = containerRef.value.scrollTop;
};
</script>

<template>
    <!-- Simple list for small collections -->
    <div v-if="!isVirtualized" class="space-y-2">
        <ShoppingItem
            v-for="item in items"
            :key="item.id"
            :item="item"
        />
    </div>

    <!-- Virtualized list for large collections (100+ items) -->
    <div
        v-else
        ref="containerRef"
        class="relative overflow-y-auto"
        :style="{ maxHeight: `${VIEWPORT_HEIGHT}px` }"
        @scroll="onScroll"
    >
        <div
            class="relative"
            :style="{ height: `${totalHeight}px` }"
        >
            <div
                v-for="row in visibleItems"
                :key="row.item.id"
                class="absolute left-0 right-0"
                :style="{ top: `${row.index * ITEM_HEIGHT}px` }"
            >
                <ShoppingItem :item="row.item" />
            </div>
        </div>
    </div>
</template>

