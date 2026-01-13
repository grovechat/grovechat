<script setup lang="ts">
import ShowCreateWorkspacePageAction from '@/actions/App/Actions/Manage/ShowCreateWorkspacePageAction';
import fallbackLogoUrl from '@/assets/images/logo.png';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
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
  DropdownMenuGroup,
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
import { getCurrentWorkspace, getGeneralSetting, logout } from '@/routes';
import contact from '@/routes/contact';
import { edit } from '@/routes/profile';
import stats from '@/routes/stats';
import workspace from '@/routes/workspace';
import type { BreadcrumbItemType, NavItem } from '@/types';
import type { WorkspaceData } from '@/types/generated';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
  BarChart,
  BookOpen,
  Building2,
  Check,
  ChevronsUpDown,
  GitBranch,
  LayoutGrid,
  LogOut,
  Pin,
  Plus,
  Settings,
  Users,
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
const currentWorkspace = computed(() => page.props.currentWorkspace);
const user = computed(() => page.props.auth.user);
const workspaces = computed(() => page.props.workspaces);

const showAvatar = computed(() => user.value?.avatar && user.value.avatar !== '');

const isExternalLink = (href: NavItem['href']) => {
  const url = toUrl(href);
  return url.startsWith('http://') || url.startsWith('https://');
};

type MainNavItem = NavItem & {
  /**
   * 这些 URL 命中任意一个（前缀匹配）则视为该一级菜单激活
   * 用于“二级导航切换后父级仍保持选中”的场景
   */
  activeUrls?: string[];
};

const contactsBaseUrl = computed(() => {
  const sample = contact.index.url({
    slug: currentWorkspace.value.slug,
    type: '__type__',
  });
  return sample.replace('/__type__/index', '');
});

const manageBaseUrl = computed(() => {
  // /w/{slug}/manage/workspaces/current -> /w/{slug}/manage
  return getCurrentWorkspace
    .url(currentWorkspace.value.slug)
    .replace(/\/workspaces\/current$/, '');
});

const mainNavItems = computed<MainNavItem[]>(() => [
  {
    title: t('工作台'),
    href: workspace.dashboard.url(currentWorkspace.value.slug),
    icon: LayoutGrid,
  },
  {
    title: t('联系人'),
    href: contact.index.url({ slug: currentWorkspace.value.slug, type: 'all' }),
    icon: Users,
    activeUrls: [
      contactsBaseUrl.value,
      contact.conversations.url(currentWorkspace.value.slug),
    ],
  },
  {
    title: t('统计'),
    href: stats.index.url(currentWorkspace.value.slug),
    icon: BarChart,
    activeUrls: [stats.index.url(currentWorkspace.value.slug)],
  },
  {
    title: t('管理中心'),
    href: getCurrentWorkspace.url(currentWorkspace.value.slug),
    icon: Building2,
    activeUrls: [manageBaseUrl.value],
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
  {
    title: t('文档'),
    href: 'https://docs.grovechat.com',
    icon: BookOpen,
  },
  {
    title: t('系统设置'),
    href: getGeneralSetting.url(currentWorkspace.value.slug),
    icon: Settings,
  },
]);

const switchWorkspace = (selectedWorkspace: WorkspaceData) => {
  if (selectedWorkspace.slug !== currentWorkspace.value?.slug) {
    router.visit(workspace.dashboard.url(selectedWorkspace.slug), {
      preserveState: false,
      preserveScroll: false,
    });
  }
};

const goToCreateWorkspace = () => {
  if (currentWorkspace.value) {
    router.visit(ShowCreateWorkspacePageAction.url(currentWorkspace.value.slug));
  }
};

const handleLogout = () => {
  router.flushAll();
};
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
                    :href="workspace.dashboard.url(currentWorkspace.slug)"
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
                    <span class="text-sm leading-tight font-semibold">{{
                      systemName
                    }}</span>

                    <DropdownMenu v-if="currentWorkspace">
                      <DropdownMenuTrigger as-child>
                        <button
                          class="flex w-full items-center gap-1 rounded-md py-1 text-left text-xs transition-colors hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                        >
                          <div
                            class="flex h-5 w-5 shrink-0 items-center justify-center overflow-hidden rounded text-sidebar-primary-foreground"
                          >
                            <img
                              :src="currentWorkspace.logo_url"
                              :alt="currentWorkspace.name"
                              class="h-full w-full object-cover"
                            />
                          </div>
                          <span class="flex-1 truncate text-xs font-medium">
                            {{ currentWorkspace.name }}
                          </span>
                          <ChevronsUpDown class="h-3 w-3 shrink-0 opacity-50" />
                        </button>
                      </DropdownMenuTrigger>
                      <DropdownMenuContent align="start" class="w-64">
                        <div
                          class="px-2 py-1.5 text-xs font-semibold text-muted-foreground"
                        >
                          {{ t('切换工作区') }}
                        </div>
                        <DropdownMenuItem
                          v-for="w in workspaces"
                          :key="w.id"
                          class="flex cursor-pointer items-center gap-2"
                          @click="switchWorkspace(w)"
                        >
                          <div
                            class="flex h-6 w-6 shrink-0 items-center justify-center overflow-hidden rounded-md text-sidebar-primary-foreground"
                          >
                            <img
                              :src="w.logo_url"
                              :alt="w.name"
                              class="h-full w-full object-cover"
                            />
                          </div>
                          <span class="flex-1 truncate">{{ w.name }}</span>
                          <Check
                            v-if="w.slug === currentWorkspace?.slug"
                            class="h-4 w-4 shrink-0"
                          />
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                          class="flex cursor-pointer items-center gap-2"
                          @click="goToCreateWorkspace"
                        >
                          <div
                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md border border-dashed"
                          >
                            <Plus class="h-4 w-4" />
                          </div>
                          <span>{{ t('添加工作区') }}</span>
                        </DropdownMenuItem>
                      </DropdownMenuContent>
                    </DropdownMenu>
                  </div>

                  <div
                    v-if="page.props.currentWorkspace"
                    class="hidden group-data-[collapsible=icon]:block"
                  >
                    <div
                      class="flex h-6 w-6 items-center justify-center overflow-hidden rounded bg-sidebar-primary text-sidebar-primary-foreground"
                    >
                      <img
                        :src="currentWorkspace.logo_url"
                        :alt="page.props.currentWorkspace.name"
                        class="h-full w-full object-cover"
                      />
                    </div>
                  </div>
                </div>
              </SidebarMenuItem>
            </SidebarMenu>
            <Button
              variant="ghost"
              size="icon"
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
            </Button>
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
                  <Link :href="item.href">
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
                <SidebarMenuItem
                  v-for="item in footerNavItems"
                  :key="item.title"
                >
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
                      <AvatarFallback
                        class="rounded-lg text-black dark:text-white"
                      >
                        {{ getInitials(user.name) }}
                      </AvatarFallback>
                    </Avatar>

                    <div class="grid flex-1 text-left text-sm leading-tight">
                      <span class="truncate font-medium">{{ user.name }}</span>
                    </div>

                    <ChevronsUpDown class="ml-auto size-4" />
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
                    <div
                      class="flex items-center gap-2 px-1 py-1.5 text-left text-sm"
                    >
                      <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
                        <AvatarImage
                          v-if="showAvatar"
                          :src="user.avatar!"
                          :alt="user.name"
                        />
                        <AvatarFallback
                          class="rounded-lg text-black dark:text-white"
                        >
                          {{ getInitials(user.name) }}
                        </AvatarFallback>
                      </Avatar>

                      <div class="grid flex-1 text-left text-sm leading-tight">
                        <span class="truncate font-medium">{{ user.name }}</span>
                        <span class="truncate text-xs text-muted-foreground">{{
                          user.email
                        }}</span>
                      </div>
                    </div>
                  </DropdownMenuLabel>
                  <DropdownMenuSeparator />
                  <DropdownMenuGroup>
                    <DropdownMenuItem :as-child="true">
                      <Link
                        class="block w-full"
                        :href="edit(currentWorkspace.slug)"
                        prefetch
                        as="button"
                      >
                        <Settings class="mr-2 h-4 w-4" />
                        {{ t('个人资料') }}
                      </Link>
                    </DropdownMenuItem>
                  </DropdownMenuGroup>
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
                  <BreadcrumbSeparator v-if="index !== props.breadcrumbs.length - 1" />
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
