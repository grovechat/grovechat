<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import WorkspaceSwitcher from '@/components/WorkspaceSwitcher.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const systemName = computed(() => page.props.generalSettings?.name || 'GroveChat');
</script>

<template>
  <div class="flex items-start gap-2 w-full">
    <!-- 系统 Logo -->
    <div
      class="flex aspect-square size-10 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground shrink-0"
    >
      <AppLogoIcon class="size-10 fill-current text-white dark:text-black" />
    </div>

    <!-- 右侧：系统名称和工作区切换器 -->
    <div class="flex flex-col gap-1 flex-1 min-w-0">
      <!-- 系统名称 -->
      <span class="text-sm font-semibold leading-tight">{{ systemName }}</span>

      <!-- 工作区切换器 -->
      <WorkspaceSwitcher />
    </div>
  </div>
</template>
