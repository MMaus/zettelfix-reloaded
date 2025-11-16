<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3';
import type { ShoppingListItem } from '@/types/shopping';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';

interface Props {
    item: ShoppingListItem;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.item.name,
    quantity: props.item.quantity,
    categories: props.item.categories || [],
    in_basket: props.item.in_basket,
});

const toggleBasket = () => {
    form.in_basket = !form.in_basket;
    form.put(`/shopping/${props.item.id}`, {
        preserveScroll: true,
        preserveState: true,
    });
};

const deleteItem = () => {
    if (confirm('Are you sure you want to delete this item?')) {
        router.delete(`/shopping/${props.item.id}`);
    }
};

const formatDate = (dateString: string | null | undefined): string => {
    if (!dateString) return '';
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
    <Card 
        :class="{ 
            'opacity-60 border-primary': item.in_basket,
            'cursor-pointer hover:bg-muted/50': true
        }"
        @click="toggleBasket"
    >
        <CardHeader>
            <div class="flex items-start justify-between">
                <div class="flex-1 flex items-start gap-3">
                    <Checkbox 
                        :checked="item.in_basket" 
                        @click.stop
                        @update:checked="toggleBasket"
                        class="mt-1"
                    />
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <CardTitle class="text-lg">{{ item.name }}</CardTitle>
                            <span v-if="item.in_basket" class="rounded-full bg-primary px-2 py-0.5 text-xs text-primary-foreground">
                                In Basket
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Quantity: {{ item.quantity }}
                        </p>
                    </div>
                </div>
                <div class="ml-4 flex gap-2" @click.stop>
                    <Link :href="`/shopping/${item.id}/edit`">
                        <Button variant="outline" size="sm">Edit</Button>
                    </Link>
                    <Button variant="destructive" size="sm" @click="deleteItem">
                        Delete
                    </Button>
                </div>
            </div>
        </CardHeader>
        <CardContent>
            <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                <div v-if="item.categories && item.categories.length > 0" class="flex flex-wrap gap-1">
                    <span
                        v-for="category in item.categories"
                        :key="category"
                        class="rounded-full bg-muted px-2 py-0.5 text-xs"
                    >
                        {{ category }}
                    </span>
                </div>
                <div class="text-xs">
                    Added: {{ formatDate(item.created_at) }}
                </div>
            </div>
        </CardContent>
    </Card>
</template>

