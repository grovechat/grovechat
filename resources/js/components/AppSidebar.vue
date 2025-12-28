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
  useSidebar,
} from '@/components/ui/sidebar';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, GitBranch, LayoutGrid, Settings, Pin } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

const { t } = useI18n();
const { tenantPath } = useTenant();
const { toggleSidebar, state } = useSidebar();

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
    <SidebarHeader class="group-data-[collapsible=icon]:!p-0">
      <div class="flex items-center justify-between group-data-[collapsible=icon]:flex-col">
        <SidebarMenu class="group-data-[collapsible=icon]:!p-2">
          <SidebarMenuItem>
            <SidebarMenuButton size="lg" as-child>
              <Link :href="tenantPath ? dashboard(tenantPath) : '/'">
                <AppLogo />
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
        <Button
          variant="ghost"
          size="icon"
          :class="cn(
            'h-7 w-7 mr-2 shrink-0 transition-colors duration-200',
            'group-data-[collapsible=icon]:mr-0 group-data-[collapsible=icon]:mb-2',
            'group-data-[state=expanded]/sidebar-wrapper:bg-sidebar-accent group-data-[state=expanded]/sidebar-wrapper:text-sidebar-accent-foreground'
          )"
          @click="toggleSidebar"
        >
          <Pin 
            :class="cn(
              'h-4 w-4 transition-all duration-200',
              'group-data-[state=collapsed]/sidebar-wrapper:rotate-45'
            )" 
            :fill="state === 'expanded' ? 'currentColor' : 'none'"
          />
          <span class="sr-only">Toggle Sidebar</span>
        </Button>
      </div>
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
