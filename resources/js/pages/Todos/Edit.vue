<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import type { TodoItem } from '@/types/todo';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Props {
    todo: TodoItem;
    canUpdate: boolean;
    canDelete: boolean;
}

const props = defineProps<Props>();

const form = useForm({
    title: props.todo.title,
    description: props.todo.description || '',
    tags: props.todo.tags || [],
    due_date: props.todo.due_date || '',
});

const tagInput = ref('');
const tags = ref<string[]>(props.todo.tags || []);

onMounted(() => {
    tags.value = props.todo.tags || [];
});

const addTag = () => {
    const tag = tagInput.value.trim();
    if (tag && !tags.value.includes(tag)) {
        tags.value.push(tag);
        tagInput.value = '';
    }
};

const removeTag = (tagToRemove: string) => {
    tags.value = tags.value.filter(tag => tag !== tagToRemove);
};

const submit = () => {
    form.tags = tags.value;
    form.put(`/todos/${props.todo.id}`);
};

const deleteTodo = () => {
    if (confirm('Are you sure you want to delete this todo?')) {
        form.delete(`/todos/${props.todo.id}`);
    }
};

const breadcrumbs = [
    { title: 'Todos', href: '/todos' },
    { title: 'Edit', href: `/todos/${props.todo.id}/edit` },
];
</script>

<template>
    <Head :title="`Edit: ${todo.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto p-4 md:p-6 max-w-2xl">
            <h1 class="mb-6 text-2xl font-bold md:text-3xl">Edit Todo</h1>

            <Card>
                <CardHeader>
                    <CardTitle>Edit Todo Item</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="title">Title *</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                type="text"
                                required
                                class="mt-1"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <p v-if="form.errors.title" class="mt-1 text-sm text-red-500">
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="mt-1 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                                :class="{ 'border-red-500': form.errors.description }"
                            />
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div>
                            <Label for="tags">Tags</Label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                <span
                                    v-for="tag in tags"
                                    :key="tag"
                                    class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-2 py-1 text-sm"
                                >
                                    {{ tag }}
                                    <button
                                        type="button"
                                        @click="removeTag(tag)"
                                        class="hover:text-primary-foreground"
                                    >
                                        ×
                                    </button>
                                </span>
                            </div>
                            <div class="mt-2 flex gap-2">
                                <Input
                                    v-model="tagInput"
                                    type="text"
                                    placeholder="Add tag"
                                    @keydown.enter.prevent="addTag"
                                    class="flex-1"
                                />
                                <Button type="button" @click="addTag" variant="outline">
                                    Add
                                </Button>
                            </div>
                            <p v-if="form.errors.tags" class="mt-1 text-sm text-red-500">
                                {{ form.errors.tags }}
                            </p>
                        </div>

                        <div>
                            <Label for="due_date">Due Date</Label>
                            <Input
                                id="due_date"
                                v-model="form.due_date"
                                type="date"
                                class="mt-1"
                                :class="{ 'border-red-500': form.errors.due_date }"
                            />
                            <p v-if="form.errors.due_date" class="mt-1 text-sm text-red-500">
                                {{ form.errors.due_date }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Updating...' : 'Update Todo' }}
                            </Button>
                            <Link
                                href="/todos"
                                class="inline-flex"
                            >
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </Link>
                            <Button
                                v-if="canDelete"
                                type="button"
                                variant="destructive"
                                @click="deleteTodo"
                                :disabled="form.processing"
                            >
                                Delete
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

