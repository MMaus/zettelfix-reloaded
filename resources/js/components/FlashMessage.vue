<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { CheckCircle2, AlertCircle, Info, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const page = usePage();
const visible = ref(false);
const message = ref<string | null>(null);
const type = ref<'success' | 'error' | 'info'>('success');

const flash = computed(() => page.props.flash as {
    success?: string;
    error?: string;
    info?: string;
} | undefined);

watch(flash, (newFlash) => {
    if (newFlash?.success) {
        message.value = newFlash.success;
        type.value = 'success';
        visible.value = true;
        setTimeout(() => {
            visible.value = false;
        }, 5000);
    } else if (newFlash?.error) {
        message.value = newFlash.error;
        type.value = 'error';
        visible.value = true;
        setTimeout(() => {
            visible.value = false;
        }, 7000);
    } else if (newFlash?.info) {
        message.value = newFlash.info;
        type.value = 'info';
        visible.value = true;
        setTimeout(() => {
            visible.value = false;
        }, 5000);
    }
}, { immediate: true, deep: true });

const icon = computed(() => {
    switch (type.value) {
        case 'success':
            return CheckCircle2;
        case 'error':
            return AlertCircle;
        case 'info':
            return Info;
        default:
            return Info;
    }
});

const variant = computed(() => {
    switch (type.value) {
        case 'success':
            return 'default';
        case 'error':
            return 'destructive';
        case 'info':
            return 'default';
        default:
            return 'default';
    }
});

const close = () => {
    visible.value = false;
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
    >
        <div
            v-if="visible && message"
            class="fixed top-4 right-4 z-50 w-full max-w-md"
        >
            <Alert :variant="variant" class="shadow-lg">
                <component :is="icon" class="h-4 w-4" />
                <AlertDescription class="flex items-center justify-between pr-2">
                    <span>{{ message }}</span>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="h-6 w-6"
                        @click="close"
                    >
                        <X class="h-4 w-4" />
                    </Button>
                </AlertDescription>
            </Alert>
        </div>
    </Transition>
</template>

