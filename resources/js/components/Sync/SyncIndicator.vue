<script setup lang="ts">
import { computed } from 'vue';
import type { SyncStatus } from '@/types/sync';
import { CheckCircle2, AlertCircle, Loader2, WifiOff } from 'lucide-vue-next';

interface Props {
    status: SyncStatus;
}

const props = defineProps<Props>();

const statusIcon = computed(() => {
    if (!props.status.isOnline) {
        return WifiOff;
    }
    if (props.status.syncInProgress) {
        return Loader2;
    }
    if (props.status.pendingChanges > 0) {
        return AlertCircle;
    }
    return CheckCircle2;
});

const statusColor = computed(() => {
    if (!props.status.isOnline) {
        return 'text-muted-foreground';
    }
    if (props.status.syncInProgress) {
        return 'text-primary animate-spin';
    }
    if (props.status.pendingChanges > 0) {
        return 'text-yellow-500';
    }
    return 'text-green-500';
});

const statusText = computed(() => {
    if (!props.status.isOnline) {
        return 'Offline';
    }
    if (props.status.syncInProgress) {
        return 'Syncing...';
    }
    if (props.status.pendingChanges > 0) {
        return `${props.status.pendingChanges} pending`;
    }
    if (props.status.lastSyncedAt) {
        const lastSynced = new Date(props.status.lastSyncedAt);
        const minutesAgo = Math.floor((Date.now() - lastSynced.getTime()) / 60000);
        if (minutesAgo < 1) {
            return 'Just now';
        }
        if (minutesAgo < 60) {
            return `${minutesAgo}m ago`;
        }
        return 'Synced';
    }
    return 'Not synced';
});
</script>

<template>
    <div class="flex items-center gap-2 text-sm text-muted-foreground">
        <component :is="statusIcon" :class="['h-4 w-4', statusColor]" />
        <span>{{ statusText }}</span>
    </div>
</template>

