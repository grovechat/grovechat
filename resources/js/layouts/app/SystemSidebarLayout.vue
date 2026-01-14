<script setup lang="ts">
import fallbackLogoUrl from '@/assets/images/logo.png';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupContent,
  SidebarHeader,
  SidebarInset,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarProvider,
  SidebarTrigger,
  useSidebar,
} from '@/components/ui/sidebar';
import Toaster from '@/components/ui/toast/Toaster.vue';
import { useI18n } from '@/composables/useI18n';
import { getInitials } from '@/composables/useInitials';
import { useErrorHandling } from '@/composables/useToast';
import { cn, toUrl, urlIsActive } from '@/lib/utils';
import { getGeneralSetting, getStorageSetting, getWorkspaceList, logout } from '@/routes';
import { edit as editProfile } from '@/routes/profile';
import systemSetting from '@/routes/system-setting';
import type { BreadcrumbItemType, NavItem } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
  Building2,
  Database,
  GitBranch,
  LogOut,
  Mail,
  Pin,
  Plug,
  Settings,
  Shield,
  Wrench,
} from 'lucide-vue-next';
import { computed, defineComponent } from 'vue';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

const props = withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const { t } = useI18n();
const page = usePage();
const isOpen = page.props.sidebarOpen;
useErrorHandling();

// 注意：useSidebar() 依赖 <Sidebar> 提供的注入，不能在父级 layout 的 setup() 里直接调用。
// 需要放到 <Sidebar> 的子树里调用（通过一个局部 slot consumer 组件）。
const SidebarContext = defineComponent({
  name: 'SidebarContextConsumer',
  setup(_, { slots }) {
    const sidebar = useSidebar();
    return () => (slots.default ? slots.default(sidebar) : null);
  },
});

const generalSettings = computed(() => page.props.generalSettings);
const systemName = computed(() => generalSettings.value?.name || 'GroveChat');
const systemLogo = computed(
  () => generalSettings.value?.logo_url || fallbackLogoUrl,
);
const user = computed(() => page.props.auth.user);
const fromWorkspaceSlug = computed(() => page.props.fromWorkspaceSlug);
const showAvatar = computed(() => user.value?.avatar && user.value.avatar !== '');

type MainNavItem = NavItem & {
  activeUrls?: string[];
};

const mainNavItems = computed<MainNavItem[]>(() => [
  {
    title: t('基础设置'),
    href: getGeneralSetting.url(),
    icon: Settings,
    activeUrls: ['/system-settings/general'],
  },
  {
    title: t('工作区管理'),
    href: getWorkspaceList.url(),
    icon: Building2,
    activeUrls: ['/system-settings/workspaces'],
  },
  {
    title: t('存储设置'),
    href: getStorageSetting.url(),
    icon: Database,
    activeUrls: ['/system-settings/storage'],
  },
  {
    title: t('邮箱服务器'),
    href: systemSetting.getMailSettings.url(),
    icon: Mail,
    activeUrls: ['/system-settings/mail'],
  },
  {
    title: t('集成'),
    href: systemSetting.getIntegrationSettings.url(),
    icon: Plug,
    activeUrls: ['/system-settings/integration'],
  },
  {
    title: t('安全'),
    href: systemSetting.getSecuritySettings.url(),
    icon: Shield,
    activeUrls: ['/system-settings/security'],
  },
  {
    title: t('维护'),
    href: systemSetting.getMaintenanceSettings.url(),
    icon: Wrench,
    activeUrls: ['/system-settings/maintenance'],
  },
]);

const isMainNavItemActive = (item: MainNavItem) => {
  if (item.activeUrls && item.activeUrls.length > 0) {
    return item.activeUrls.some((u) => urlIsActive(u, page.url, { mode: 'prefix' }));
  }

  return urlIsActive(item.href, page.url);
};

const footerNavItems = computed<NavItem[]>(() => [
  {
    title: t('GitHub仓库'),
    href: 'https://github.com/grovechat/grovechat',
    icon: GitBranch,
  },
]);

const isExternalLink = (href: NavItem['href']) => {
  const url = toUrl(href);
  return url.startsWith('http://') || url.startsWith('https://');
};

const handleLogout = () => {
  router.flushAll();
};

const profileHref = computed(() => editProfile.url({ query: { context: 'system' } }));
</script>

<template>
  <SidebarProvider :default-open="isOpen">
    <Sidebar collapsible="icon" variant="inset">
      <SidebarContext v-slot="{ toggleSidebar, state, isMobile }">
        <SidebarHeader class="group-data-[collapsible=icon]:p-0!">
          <div
            class="flex items-center justify-between group-data-[collapsible=icon]:flex-col group-data-[collapsible=icon]:gap-2"
          >
            <SidebarMenu class="w-full group-data-[collapsible=icon]:p-2!">
              <SidebarMenuItem>
                <div
                  class="flex w-full items-center gap-2 px-0 py-0 group-data-[collapsible=icon]:flex-col group-data-[collapsible=icon]:items-center"
                >
                  <Link
                    :href="getGeneralSetting.url()"
                    class="shrink-0 p-2 group-data-[collapsible=icon]:p-0"
                  >
                    <div
                      class="flex aspect-square size-12 items-center justify-center rounded-md text-sidebar-primary-foreground"
                    >
                      <img
                        :src="systemLogo"
                        :alt="systemName + ' Logo'"
                        class="size-12 object-contain"
                      />
                    </div>
                  </Link>

                  <div
                    class="flex min-w-0 flex-1 flex-col gap-1 pr-2 group-data-[collapsible=icon]:hidden"
                  >
                    <span class="text-sm leading-tight font-semibold">
                      {{ systemName }}
                    </span>
                    <span class="text-xs text-muted-foreground">
                      {{ t('系统管理') }}
                    </span>
                  </div>
                </div>
              </SidebarMenuItem>
            </SidebarMenu>

            <button
              type="button"
              :class="
                cn(
                  'h-7 w-7 shrink-0 transition-colors duration-200',
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
            </button>
          </div>
        </SidebarHeader>

        <SidebarContent>
          <SidebarGroup class="px-2 py-0">
            <SidebarMenu>
              <SidebarMenuItem v-for="item in mainNavItems" :key="item.title">
                <SidebarMenuButton
                  as-child
                  :is-active="isMainNavItemActive(item)"
                  :tooltip="item.title"
                >
                  <Link :href="toUrl(item.href)">
                    <component :is="item.icon" />
                    <span>{{ item.title }}</span>
                  </Link>
                </SidebarMenuButton>
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
          <SidebarGroup class="group-data-[collapsible=icon]:p-0">
            <SidebarGroupContent>
              <SidebarMenu>
                <SidebarMenuItem v-for="item in footerNavItems" :key="item.title">
                  <SidebarMenuButton
                    class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                    as-child
                  >
                    <a
                      v-if="isExternalLink(item.href)"
                      :href="toUrl(item.href)"
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      <component :is="item.icon" />
                      <span>{{ item.title }}</span>
                    </a>
                    <Link v-else :href="toUrl(item.href)">
                      <component :is="item.icon" />
                      <span>{{ item.title }}</span>
                    </Link>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              </SidebarMenu>
            </SidebarGroupContent>
          </SidebarGroup>

          <SidebarMenu>
            <SidebarMenuItem>
              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <SidebarMenuButton
                    size="lg"
                    class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    data-test="sidebar-menu-button"
                  >
                    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
                      <AvatarImage
                        v-if="showAvatar"
                        :src="user.avatar!"
                        :alt="user.name"
                      />
                      <AvatarFallback class="rounded-lg text-black dark:text-white">
                        {{ getInitials(user.name) }}
                      </AvatarFallback>
                    </Avatar>

                    <div class="grid flex-1 text-left text-sm leading-tight">
                      <span class="truncate font-medium">{{ user.name }}</span>
                    </div>
                  </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                  class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                  :side="
                    isMobile
                      ? 'bottom'
                      : state === 'collapsed'
                        ? 'left'
                        : 'bottom'
                  "
                  align="end"
                  :side-offset="4"
                >
                  <DropdownMenuLabel class="p-0 font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                      <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
                        <AvatarImage
                          v-if="showAvatar"
                          :src="user.avatar!"
                          :alt="user.name"
                        />
                        <AvatarFallback class="rounded-lg text-black dark:text-white">
                          {{ getInitials(user.name) }}
                        </AvatarFallback>
                      </Avatar>

                      <div class="grid flex-1 text-left text-sm leading-tight">
                        <span class="truncate font-medium">{{ user.name }}</span>
                        <span class="truncate text-xs text-muted-foreground">
                          {{ user.email }}
                        </span>
                      </div>
                    </div>
                  </DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem :as-child="true">
                    <Link class="block w-full" :href="profileHref" as="button">
                      <Settings class="mr-2 h-4 w-4" />
                      {{ t('个人设置') }}
                    </Link>
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem :as-child="true">
                    <Link
                      class="block w-full"
                      :href="logout()"
                      @click="handleLogout"
                      as="button"
                      data-test="logout-button"
                    >
                      <LogOut class="mr-2 h-4 w-4" />
                      {{ t('退出登录') }}
                    </Link>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarFooter>
      </SidebarContext>
    </Sidebar>

    <SidebarInset class="overflow-x-hidden">
      <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
      >
        <SidebarTrigger class="md:hidden" />
        <div class="flex items-center gap-2">
          <template v-if="props.breadcrumbs && props.breadcrumbs.length > 0">
            <Breadcrumb>
              <BreadcrumbList>
                <template v-for="(item, index) in props.breadcrumbs" :key="index">
                  <BreadcrumbItem>
                    <template v-if="index === props.breadcrumbs.length - 1">
                      <BreadcrumbPage>{{ item.title }}</BreadcrumbPage>
                    </template>
                    <template v-else>
                      <BreadcrumbLink as-child>
                        <Link :href="item.href ?? '#'">{{ item.title }}</Link>
                      </BreadcrumbLink>
                    </template>
                  </BreadcrumbItem>
                  <BreadcrumbSeparator
                    v-if="index !== props.breadcrumbs.length - 1"
                  />
                </template>
              </BreadcrumbList>
            </Breadcrumb>
          </template>
        </div>
      </header>

      <slot />
    </SidebarInset>

    <Toaster />
  </SidebarProvider>
</template>

