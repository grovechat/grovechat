<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useI18n } from '@/composables/useI18n';
import { useWorkspace } from '@/composables/useWorkspace';
import { toUrl, urlIsActive } from '@/lib/utils';
import systemSetting from '@/routes/system-setting';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { workspaceSlug } = useWorkspace();

const sidebarNavItems = computed<NavItem[]>(() => {
  if (!workspaceSlug.value) return [];

  return [
    {
      title: t('基础设置'),
      href: systemSetting.getGeneralSettings.url(workspaceSlug.value),
    },
    {
      title: t('存储设置'),
      href: systemSetting.getStorageSettings.url(workspaceSlug.value),
    },
    {
      title: t('邮箱服务器'),
      href: systemSetting.getMailSettings.url(workspaceSlug.value),
    },
    {
      title: t('集成'),
      href: systemSetting.getIntegrationSettings.url(workspaceSlug.value),
    },
    {
      title: t('安全'),
      href: systemSetting.getSecuritySettings.url(workspaceSlug.value),
    },
    {
      title: t('维护'),
      href: systemSetting.getMaintenanceSettings.url(workspaceSlug.value),
    },
  ];
});

const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
</script>

<template>
  <div class="flex flex-1 flex-col lg:flex-row">
    <aside class="w-full lg:w-48 lg:self-stretch">
      <nav
        class="flex h-full flex-col space-y-3 border-r border-border/40 bg-card/50 p-4 shadow-sm backdrop-blur-sm"
      >
          <div class="space-y-0.5">
            <h2 class="text-xl font-semibold tracking-tight">
              {{ t('系统设置') }}
            </h2>
            <p class="text-sm text-muted-foreground">
              {{ t('管理系统的配置和设置') }}
            </p>
          </div>

          <div class="flex flex-col space-y-1">
            <Button
              v-for="item in sidebarNavItems"
              :key="toUrl(item.href)"
              variant="ghost"
              :class="[
                'w-full justify-start',
                { 'bg-muted': urlIsActive(item.href, currentPath) },
              ]"
              as-child
            >
              <Link
                :href="typeof item.href === 'string' ? item.href : item.href"
              >
                {{ item.title }}
              </Link>
            </Button>
          </div>
      </nav>
    </aside>

    <Separator class="my-6 lg:hidden" />

    <div class="flex-1 px-4 py-6">
      <section class="max-w-2xl space-y-12">
        <slot />
      </section>
    </div>
  </div>
</template>
