<script setup lang="ts">
import { useI18n } from '@/composables/useI18n';
import SidebarShell, { type SidebarShellNavItem } from '@/layouts/app/SidebarShell.vue';
import { getGeneralSetting, getStorageSetting, getWorkspaceList } from '@/routes';
import { edit as editProfile } from '@/routes/profile';
import systemSetting from '@/routes/system-setting';
import type { BreadcrumbItemType, NavItem } from '@/types';
import {
  Building2,
  Database,
  BookOpen,
  GitBranch,
  Mail,
  Plug,
  Settings,
  Shield,
  Wrench,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

const props = withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const { t } = useI18n();

const mainNavItems = computed<SidebarShellNavItem[]>(() => [
  {
    title: t('基础设置'),
    href: getGeneralSetting.url(),
    icon: Settings,
    activeUrls: ['/admin/general'],
  },
  {
    title: t('工作区管理'),
    href: getWorkspaceList.url(),
    icon: Building2,
    activeUrls: ['/admin/workspaces'],
  },
  {
    title: t('存储设置'),
    href: getStorageSetting.url(),
    icon: Database,
    activeUrls: ['/admin/storage'],
  },
  {
    title: t('邮箱服务器'),
    href: systemSetting.getMailSettings.url(),
    icon: Mail,
    activeUrls: ['/admin/mail'],
  },
  {
    title: t('集成'),
    href: systemSetting.getIntegrationSettings.url(),
    icon: Plug,
    activeUrls: ['/admin/integration'],
  },
  {
    title: t('安全'),
    href: systemSetting.getSecuritySettings.url(),
    icon: Shield,
    activeUrls: ['/admin/security'],
  },
  {
    title: t('维护'),
    href: systemSetting.getMaintenanceSettings.url(),
    icon: Wrench,
    activeUrls: ['/admin/maintenance'],
  },
]);

const footerNavItems = computed<NavItem[]>(() => [
  {
    title: t('GitHub仓库'),
    href: 'https://github.com/grovechat/grovechat',
    icon: GitBranch,
  },
  {
    title: t('文档'),
    href: 'https://docs.grovechat.com',
    icon: BookOpen,
  },
]);

const profileHref = computed(() => editProfile.url());
</script>

<template>
  <SidebarShell
    :breadcrumbs="props.breadcrumbs"
    :header-href="getGeneralSetting.url()"
    :header-subtitle="t('系统管理')"
    :main-nav-items="mainNavItems"
    :footer-nav-items="footerNavItems"
    :profile-href="profileHref"
    :profile-label="t('个人设置')"
  >
    <slot />
  </SidebarShell>
</template>

