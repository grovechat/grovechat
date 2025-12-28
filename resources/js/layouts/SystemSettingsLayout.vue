<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import { toUrl, urlIsActive } from '@/lib/utils';
import systemSetting from '@/routes/system-setting';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { tenantPath } = useTenant();

const sidebarNavItems = computed<NavItem[]>(() => {
  if (!tenantPath.value) return [];

  return [
    {
      title: t('基础设置'),
      href: systemSetting.getGeneralSettings.url(tenantPath.value),
    },
    {
      title: t('存储设置'),
      href: systemSetting.getStorageSettings.url(tenantPath.value),
    },
    {
      title: t('邮箱服务器'),
      href: systemSetting.getMailSettings.url(tenantPath.value),
    },
    {
      title: t('集成'),
      href: systemSetting.getIntegrationSettings.url(tenantPath.value),
    },
    {
      title: t('安全'),
      href: systemSetting.getSecuritySettings.url(tenantPath.value),
    },
    {
      title: t('维护'),
      href: systemSetting.getMaintenanceSettings.url(tenantPath.value),
    },
  ];
});

const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
</script>

<template>
  <div class="px-4 py-6">
    <div class="flex flex-col lg:flex-row lg:space-x-12">
      <aside class="w-full max-w-xl lg:w-48">
        <nav class="flex flex-col space-y-3 rounded-xl border border-border/40 bg-card/50 p-4 shadow-sm backdrop-blur-sm min-h-[calc(100vh-12rem)]">
          <div class="space-y-0.5">
            <h2 class="text-xl font-semibold tracking-tight">{{ t('系统设置') }}</h2>
            <p class="text-sm text-muted-foreground">{{ t('管理系统的配置和设置') }}</p>
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
            <Link :href="typeof item.href === 'string' ? item.href : item.href">
              {{ item.title }}
            </Link>
          </Button>
          </div>
        </nav>
      </aside>

      <Separator class="my-6 lg:hidden" />

      <div class="flex-1 md:max-w-2xl">
        <section class="max-w-xl space-y-12">
          <slot />
        </section>
      </div>
    </div>
  </div>
</template>
