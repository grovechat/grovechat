<script setup lang="ts">
import { useSidebar } from '@/components/ui/sidebar';

const sidebar = useSidebar();
</script>

<template>
  <slot v-bind="sidebar" />
</template>
