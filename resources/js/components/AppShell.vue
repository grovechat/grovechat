<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import Toaster from '@/components/ui/toast/Toaster.vue';
import { useFlashToast } from '@/composables/useFlashToast';
import { usePage } from '@inertiajs/vue3';

interface Props {
  variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;
useFlashToast();
</script>

<template>
  <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
    <slot />
    <Toaster />
  </div>
  <SidebarProvider v-else :default-open="isOpen">
    <slot />
    <Toaster />
  </SidebarProvider>
</template>
