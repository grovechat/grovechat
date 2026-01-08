<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TenantSwitcher from '@/components/TenantSwitcher.vue';
import { Button } from '@/components/ui/button';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuItem,
  useSidebar,
} from '@/components/ui/sidebar';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';
import contact from '@/routes/contact';
import stats from '@/routes/stats';
import systemSetting from '@/routes/system-setting';
import tenantSetting from '@/routes/tenant-setting';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
  BarChart,
  BookOpen,
  Building2,
  GitBranch,
  LayoutGrid,
  Pin,
  Settings,
  Users,
} from 'lucide-vue-next';
import { computed } from 'vue';

const { t } = useI18n();
const { tenantPath } = useTenant();
const { toggleSidebar, state } = useSidebar();
const page = usePage();

const systemName = computed(() => page.props.generalSettings?.name || 'GroveChat');

const mainNavItems = computed<NavItem[]>(() => [
  {
    title: t('工作台'),
    href: tenantPath.value ? dashboard(tenantPath.value) : '/',
    icon: LayoutGrid,
  },
  {
    title: t('联系人'),
    href: tenantPath.value
      ? contact.index.url({ tenant_path: tenantPath.value, type: 'all' })
      : '/',
    icon: Users,
  },
  {
    title: t('统计'),
    href: tenantPath.value ? stats.index.url(tenantPath.value) : '/',
    icon: BarChart,
  },
  {
    title: t('管理中心'),
    href: tenantPath.value
      ? tenantSetting.tenant.general.url(tenantPath.value)
      : '/',
    icon: Building2,
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
  {
    title: t('系统设置'),
    href: tenantPath.value
      ? systemSetting.getGeneralSettings.url(tenantPath.value)
      : '/',
    icon: Settings,
  },
]);
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader class="group-data-[collapsible=icon]:!p-0">
      <div
        class="flex items-center justify-between group-data-[collapsible=icon]:flex-col group-data-[collapsible=icon]:gap-2"
      >
        <SidebarMenu class="group-data-[collapsible=icon]:!p-2 w-full">
          <SidebarMenuItem>
            <div class="flex items-center gap-2 w-full px-2 py-2">
              <!-- 系统 Logo - 可点击跳转首页 -->
              <Link
                :href="tenantPath ? dashboard(tenantPath) : '/'"
                class="shrink-0"
              >
                <div
                  class="flex aspect-square size-10 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
                >
                  <AppLogoIcon class="size-10 fill-current text-white dark:text-black" />
                </div>
              </Link>

              <!-- 右侧：系统名称和工作区切换器 -->
              <div class="flex flex-col gap-1 flex-1 min-w-0 group-data-[collapsible=icon]:hidden">
                <!-- 系统名称 -->
                <span class="text-sm font-semibold leading-tight">{{ systemName }}</span>

                <!-- 工作区切换器 - 独立的热区 -->
                <TenantSwitcher />
              </div>
            </div>
          </SidebarMenuItem>
        </SidebarMenu>
        <Button
          variant="ghost"
          size="icon"
          :class="
            cn(
              'mr-3 h-7 w-7 shrink-0 transition-colors duration-200',
              'group-data-[collapsible=icon]:mr-0 group-data-[collapsible=icon]:mb-2',
              'group-data-[state=expanded]/sidebar-wrapper:bg-sidebar-accent group-data-[state=expanded]/sidebar-wrapper:text-sidebar-accent-foreground',
            )
          "
          @click="toggleSidebar"
        >
          <Pin
            :class="
              cn(
                'h-4 w-4 transition-all duration-200',
                'group-data-[state=collapsed]/sidebar-wrapper:rotate-45',
              )
            "
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
