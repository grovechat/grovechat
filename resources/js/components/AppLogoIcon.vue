<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import logoUrl from '@/assets/images/logo.png';

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
        :src="logoUrl"
        alt="Grove Chat Logo"
        :class="className"
        v-bind="$attrs"
    />
</template>
