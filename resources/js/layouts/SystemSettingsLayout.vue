<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useI18n } from '@/composables/useI18n';
import { toUrl, urlIsActive } from '@/lib/utils';
import {
  getGeneralSetting,
  getStorageSetting,
  getWorkspaceList,
} from '@/routes';
import admin from '@/routes/admin';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
  contentClass?: string;
}

const props = defineProps<Props>();

const { t } = useI18n();

const sidebarNavItems = computed<NavItem[]>(() => {
  return [
    {
      title: t('基础设置'),
      href: getGeneralSetting.url(),
    },
    {
      title: t('工作区管理'),
      href: getWorkspaceList.url(),
    },
    {
      title: t('存储设置'),
      href: getStorageSetting.url(),
    },
    {
      title: t('用户管理'),
      href: admin.getUserList.url(),
    },
    {
      title: t('邮箱服务器'),
      href: admin.getMailSettings.url(),
    },
    {
      title: t('集成'),
      href: admin.getIntegrationSettings.url(),
    },
    {
      title: t('安全'),
      href: admin.getSecuritySettings.url(),
    },
    {
      title: t('维护'),
      href: admin.getMaintenanceSettings.url(),
    },
  ];
});

const currentPath =
  typeof window !== 'undefined' ? window.location.pathname : '';
</script>

<template>
  <div class="flex flex-1 flex-col lg:flex-row">
    <aside class="w-full lg:w-50 lg:self-stretch">
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
            <Link :href="typeof item.href === 'string' ? item.href : item.href">
              {{ item.title }}
            </Link>
          </Button>
        </div>
      </nav>
    </aside>

    <Separator class="my-6 lg:hidden" />

    <div class="flex-1 px-4 py-6 sm:px-6">
      <section
        :class="[
          'mx-auto w-full space-y-12',
          props.contentClass || 'max-w-none',
        ]"
      >
        <slot />
      </section>
    </div>
  </div>
</template>
