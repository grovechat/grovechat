<script setup lang="ts">
import {
  CheckCircle2,
  XCircle,
  AlertTriangle,
  Info,
} from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';
import Toast from './Toast.vue';
import ToastClose from './ToastClose.vue';
import ToastDescription from './ToastDescription.vue';
import ToastProvider from './ToastProvider.vue';
import ToastTitle from './ToastTitle.vue';
import ToastViewport from './ToastViewport.vue';
import ToastAction from './ToastAction.vue';

const { toasts, removeToast } = useToast();

const getToastClass = (type?: string) => {
  switch (type) {
    case 'success':
      return 'border-green-200/50 bg-white/95 dark:border-green-500/20 dark:bg-zinc-900/95 backdrop-blur-sm';
    case 'error':
      return 'border-red-200/50 bg-white/95 dark:border-red-500/20 dark:bg-zinc-900/95 backdrop-blur-sm';
    case 'warning':
      return 'border-amber-200/50 bg-white/95 dark:border-amber-500/20 dark:bg-zinc-900/95 backdrop-blur-sm';
    case 'info':
      return 'border-blue-200/50 bg-white/95 dark:border-blue-500/20 dark:bg-zinc-900/95 backdrop-blur-sm';
    default:
      return 'border-border/50 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-sm';
  }
};

const getIcon = (type?: string) => {
  switch (type) {
    case 'success':
      return CheckCircle2;
    case 'error':
      return XCircle;
    case 'warning':
      return AlertTriangle;
    case 'info':
      return Info;
    default:
      return null;
  }
};

// 柔和的图标颜色
const getIconClass = (type?: string) => {
  switch (type) {
    case 'success':
      return 'text-green-600 dark:text-green-500';
    case 'error':
      return 'text-red-600 dark:text-red-500';
    case 'warning':
      return 'text-amber-600 dark:text-amber-500';
    case 'info':
      return 'text-blue-600 dark:text-blue-500';
    default:
      return 'text-foreground/60';
  }
};
</script>

<template>
  <ToastProvider>
    <Toast
      v-for="toast in toasts"
      :key="toast.id"
      :duration="toast.duration"
      :class="getToastClass(toast.type)"
      @update:open="(open) => !open && removeToast(toast.id)"
    >
      <!-- 图标 -->
      <component
        :is="getIcon(toast.type)"
        v-if="getIcon(toast.type)"
        :class="['size-5 shrink-0', getIconClass(toast.type)]"
      />

      <!-- 内容 -->
      <div class="flex-1 space-y-1.5">
        <ToastTitle v-if="toast.title" class="text-sm font-medium leading-none tracking-tight">
          {{ toast.title }}
        </ToastTitle>
        <ToastDescription v-if="toast.description" class="text-sm text-muted-foreground leading-snug">
          {{ toast.description }}
        </ToastDescription>
        <ToastAction
          v-if="toast.action"
          :alt-text="toast.action.label"
          class="mt-2"
          @click="toast.action.onClick"
        >
          {{ toast.action.label }}
        </ToastAction>
      </div>

      <!-- 关闭按钮 -->
      <ToastClose />
    </Toast>
    <ToastViewport />
  </ToastProvider>
</template>
