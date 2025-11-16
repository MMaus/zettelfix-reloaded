<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import todos from '@/routes/todos';
import shopping from '@/routes/shopping';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ListTodo, ShoppingCart, ArrowRight } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    todoCount?: number;
    shoppingCount?: number;
}

const props = withDefaults(defineProps<Props>(), {
    todoCount: undefined,
    shoppingCount: undefined,
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4"
        >
            <div class="mb-2">
                <h1 class="text-2xl font-bold md:text-3xl">Welcome to Zettelfix</h1>
                <p class="mt-2 text-muted-foreground">
                    Manage your todos and shopping lists in one place
                </p>
            </div>

            <div class="grid auto-rows-min gap-6 md:grid-cols-2 lg:grid-cols-2">
                <!-- Todo List Tile -->
                <Card class="group transition-all hover:shadow-lg">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary"
                            >
                                <ListTodo class="size-6" />
                            </div>
                            <div class="flex-1">
                                <CardTitle>Todo List</CardTitle>
                                <CardDescription>
                                    Manage your tasks and stay organized
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    v-if="todoCount !== undefined"
                                    class="text-2xl font-bold"
                                >
                                    {{ todoCount }}
                                </p>
                                <p
                                    v-else
                                    class="text-sm text-muted-foreground"
                                >
                                    {{ isAuthenticated ? 'Loading...' : 'Sign in to view' }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{ todoCount !== undefined ? 'active todos' : '' }}
                                </p>
                            </div>
                            <Link :href="todos.index().url">
                                <Button variant="outline" class="group-hover:bg-primary group-hover:text-primary-foreground">
                                    View Todos
                                    <ArrowRight class="ml-2 size-4" />
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <!-- Shopping Cart Tile -->
                <Card class="group transition-all hover:shadow-lg">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-12 items-center justify-center rounded-lg bg-primary/10 text-primary"
                            >
                                <ShoppingCart class="size-6" />
                            </div>
                            <div class="flex-1">
                                <CardTitle>Shopping List</CardTitle>
                                <CardDescription>
                                    Keep track of your shopping items
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    v-if="shoppingCount !== undefined"
                                    class="text-2xl font-bold"
                                >
                                    {{ shoppingCount }}
                                </p>
                                <p
                                    v-else
                                    class="text-sm text-muted-foreground"
                                >
                                    {{ isAuthenticated ? 'Loading...' : 'Sign in to view' }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    {{ shoppingCount !== undefined ? 'items in list' : '' }}
                                </p>
                            </div>
                            <Link :href="shopping.index().url">
                                <Button variant="outline" class="group-hover:bg-primary group-hover:text-primary-foreground">
                                    View Shopping
                                    <ArrowRight class="ml-2 size-4" />
                                </Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
