<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import zettelfixIcon from '../../img/zettelfix-icon.png';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img
        :src="zettelfixIcon"
        alt="Zettelfix"
        :class="className"
        v-bind="$attrs"
    />
</template>
