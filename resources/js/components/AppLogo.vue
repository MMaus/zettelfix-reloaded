<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <a
        href="https://zettelfix-preview.de"
        class="ml-1 grid flex-1 text-left text-sm hover:opacity-80 transition-opacity"
    >
        <span class="mb-0.5 truncate leading-tight font-semibold"
            >Zettelfix</span
        >
    </a>
</template>
