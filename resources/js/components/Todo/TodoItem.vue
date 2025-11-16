<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import type { TodoItem } from '@/types/todo';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
interface Props {
    todo: TodoItem;
}

const props = defineProps<Props>();

const deleteTodo = () => {
    if (confirm('Are you sure you want to delete this todo?')) {
        router.delete(`/todos/${props.todo.id}`);
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
    <Card>
        <CardHeader>
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <CardTitle class="text-lg">{{ todo.title }}</CardTitle>
                    <p v-if="todo.description" class="mt-2 text-sm text-muted-foreground">
                        {{ todo.description }}
                    </p>
                </div>
                <div class="ml-4 flex gap-2">
                    <Link :href="`/todos/${todo.id}/edit`">
                        <Button variant="outline" size="sm">Edit</Button>
                    </Link>
                    <Button variant="destructive" size="sm" @click="deleteTodo">
                        Delete
                    </Button>
                </div>
            </div>
        </CardHeader>
        <CardContent>
            <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                <div v-if="todo.tags && todo.tags.length > 0" class="flex flex-wrap gap-1">
                    <span
                        v-for="tag in todo.tags"
                        :key="tag"
                        class="rounded-full bg-muted px-2 py-0.5 text-xs"
                    >
                        {{ tag }}
                    </span>
                </div>
                <div v-if="todo.due_date" class="flex items-center gap-1">
                    <span>Due: {{ formatDate(todo.due_date) }}</span>
                </div>
                <div class="text-xs">
                    Created: {{ formatDate(todo.created_at) }}
                </div>
            </div>
        </CardContent>
    </Card>
</template>

