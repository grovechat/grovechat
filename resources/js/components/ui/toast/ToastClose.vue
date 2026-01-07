<script setup lang="ts">
import { ToastClose, type ToastCloseProps } from 'reka-ui';
import { X } from 'lucide-vue-next';
import { computed, type HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<
  ToastCloseProps & { class?: HTMLAttributes['class'] }
>();

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;
  return delegated;
});
</script>

<template>
  <ToastClose
    v-bind="delegatedProps"
    :class="
      cn(
        'absolute right-3 top-3 rounded-md p-1 text-foreground/40 opacity-0 transition-all hover:text-foreground hover:bg-black/5 dark:hover:bg-white/5 focus:opacity-100 focus:outline-none focus:ring-2 focus:ring-offset-0 group-hover:opacity-100',
        props.class,
      )
    "
  >
    <X class="size-4" />
  </ToastClose>
</template>
