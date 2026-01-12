<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import WorkspaceSwitcher from '@/components/WorkspaceSwitcher.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const systemName = computed(
  () => page.props.generalSettings?.name || 'GroveChat',
);
</script>

<template>
  <div class="flex w-full items-start gap-2">
    <!-- 系统 Logo -->
    <div
      class="flex aspect-square size-10 shrink-0 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
      <AppLogoIcon class="size-10 fill-current text-white dark:text-black" />
    </div>

    <!-- 右侧：系统名称和工作区切换器 -->
    <div class="flex min-w-0 flex-1 flex-col gap-1">
      <!-- 系统名称 -->
      <span class="text-sm leading-tight font-semibold">{{ systemName }}</span>

      <!-- 工作区切换器 -->
      <WorkspaceSwitcher />
    </div>
  </div>
</template>
