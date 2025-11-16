<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const form = useForm({
    name: '',
    quantity: 1,
    categories: [] as string[],
});

const categoryInput = ref('');
const categories = ref<string[]>([]);

const addCategory = () => {
    const category = categoryInput.value.trim();
    if (category && !categories.value.includes(category)) {
        categories.value.push(category);
        categoryInput.value = '';
    }
};

const removeCategory = (categoryToRemove: string) => {
    categories.value = categories.value.filter(cat => cat !== categoryToRemove);
};

const submit = () => {
    form.categories = categories.value;
    form.post('/shopping');
};

const breadcrumbs = [
    { title: 'Shopping List', href: '/shopping' },
    { title: 'Add Item', href: '/shopping/create' },
];
</script>

<template>
    <Head title="Add Shopping Item" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 md:p-6 max-w-2xl">
            <h1 class="mb-6 text-2xl font-bold md:text-3xl">Add Shopping Item</h1>

            <Card>
                <CardHeader>
                    <CardTitle>New Shopping Item</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="name">Item Name *</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="mt-1"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <Label for="quantity">Quantity *</Label>
                            <Input
                                id="quantity"
                                v-model.number="form.quantity"
                                type="number"
                                step="0.01"
                                min="0.01"
                                required
                                class="mt-1"
                                :class="{ 'border-red-500': form.errors.quantity }"
                            />
                            <p v-if="form.errors.quantity" class="mt-1 text-sm text-red-500">
                                {{ form.errors.quantity }}
                            </p>
                        </div>

                        <div>
                            <Label for="categories">Categories</Label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                <span
                                    v-for="category in categories"
                                    :key="category"
                                    class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-2 py-1 text-sm"
                                >
                                    {{ category }}
                                    <button
                                        type="button"
                                        @click="removeCategory(category)"
                                        class="hover:text-primary-foreground"
                                    >
                                        ×
                                    </button>
                                </span>
                            </div>
                            <div class="mt-2 flex gap-2">
                                <Input
                                    v-model="categoryInput"
                                    type="text"
                                    placeholder="Add category"
                                    @keydown.enter.prevent="addCategory"
                                    class="flex-1"
                                />
                                <Button type="button" @click="addCategory" variant="outline">
                                    Add
                                </Button>
                            </div>
                            <p v-if="form.errors.categories" class="mt-1 text-sm text-red-500">
                                {{ form.errors.categories }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Adding...' : 'Add Item' }}
                            </Button>
                            <Link
                                href="/shopping"
                                class="inline-flex"
                            >
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </Link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

