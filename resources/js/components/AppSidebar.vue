<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, GitBranch, LayoutGrid, Settings } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const { t } = useI18n();
const { tenantPath } = useTenant();

const mainNavItems = computed<NavItem[]>(() => [
  {
    title: t('工作台'),
    href: tenantPath.value ? dashboard(tenantPath.value) : '/',
    icon: LayoutGrid,
  },
]);

const footerNavItems: NavItem[] = [
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
  {
    title: t('系统设置'),
    href: '#',
    icon: Settings,
  },
];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="tenantPath ? dashboard(tenantPath) : '/'">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
