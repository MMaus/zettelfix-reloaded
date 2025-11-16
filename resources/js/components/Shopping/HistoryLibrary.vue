<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import type { ShoppingHistoryItem } from '@/types/shopping';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

interface Props {
    items: ShoppingHistoryItem[];
}

const props = defineProps<Props>();

const addToShoppingList = (item: ShoppingHistoryItem) => {
    const form = useForm({
        history_item_id: item.id,
    });

    form.post('/shopping/history/add', {
        preserveScroll: true,
        onSuccess: () => {
            // Success message is handled by Inertia flash messages
        },
    });
};

const formatDate = (dateString: string): string => {
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    } catch {
        return dateString;
    }
};
</script>

<template>
    <div class="space-y-2">
        <Card
            v-for="item in items"
            :key="item.id"
            class="hover:bg-muted/50 transition-colors"
        >
            <CardHeader>
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <CardTitle class="text-lg">{{ item.name }}</CardTitle>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Quantity: {{ item.quantity }}
                        </p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            Purchased: {{ formatDate(item.purchased_at) }}
                        </p>
                    </div>
                    <Button
                        @click="addToShoppingList(item)"
                        size="sm"
                        class="ml-4"
                    >
                        Add to List
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div v-if="item.categories && item.categories.length > 0" class="flex flex-wrap gap-1">
                    <span
                        v-for="category in item.categories"
                        :key="category"
                        class="rounded-full bg-muted px-2 py-0.5 text-xs"
                    >
                        {{ category }}
                    </span>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

