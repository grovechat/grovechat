<script setup lang="ts">
import ShowCreateWorkspacePageAction from '@/actions/App/Actions/Manage/ShowCreateWorkspacePageAction';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useI18n } from '@/composables/useI18n';
import { useErrorHandling } from '@/composables/useToast';
import { useRequiredWorkspace } from '@/composables/useWorkspace';
import SidebarShell, {
  type SidebarShellNavItem,
} from '@/layouts/app/SidebarShell.vue';
import { getCurrentWorkspace, getGeneralSetting } from '@/routes';
import contact from '@/routes/contact';
import logout from '@/routes/logout';
import { edit } from '@/routes/profile';
import stats from '@/routes/stats';
import workspace from '@/routes/workspace';
import type { AppPageProps, BreadcrumbItemType, NavItem } from '@/types';
import type { WorkspaceData } from '@/types/generated';
import { router, usePage } from '@inertiajs/vue3';
import {
  BarChart,
  BookOpen,
  Building2,
  Check,
  ChevronsUpDown,
  GitBranch,
  LayoutGrid,
  Plus,
  Settings,
  Users,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

const props = withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const { t } = useI18n();
const page = usePage<AppPageProps>();
useErrorHandling();

const currentWorkspace = useRequiredWorkspace();
const user = computed(() => page.props.auth.user);
const workspaces = computed(() => page.props.workspaces);

type MainNavItem = SidebarShellNavItem;

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
  ...(user.value?.is_super_admin
    ? ([
        {
          title: t('系统设置'),
          href: getGeneralSetting.url(),
          icon: Settings,
        },
      ] as NavItem[])
    : []),
]);

const logoutHref = computed(() => logout.web.url());

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
    router.visit(
      ShowCreateWorkspacePageAction.url(currentWorkspace.value.slug),
    );
  }
};
</script>

<template>
  <SidebarShell
    :breadcrumbs="props.breadcrumbs"
    :header-href="workspace.dashboard.url(currentWorkspace.slug)"
    :header-subtitle="t('工作区')"
    :main-nav-items="mainNavItems"
    :footer-nav-items="footerNavItems"
    :profile-href="
      edit({ query: { from_workspace: currentWorkspace.slug } }).url
    "
    :profile-label="t('个人资料')"
    :logout-href="logoutHref"
  >
    <template #headerSubtitle>
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
          <div class="px-2 py-1.5 text-xs font-semibold text-muted-foreground">
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
    </template>

    <template #collapsedHeaderAddon>
      <div class="hidden group-data-[collapsible=icon]:block">
        <div
          class="flex h-6 w-6 items-center justify-center overflow-hidden rounded bg-sidebar-primary text-sidebar-primary-foreground"
        >
          <img
            :src="currentWorkspace.logo_url"
            :alt="currentWorkspace.name"
            class="h-full w-full object-cover"
          />
        </div>
      </div>
    </template>

    <slot />
  </SidebarShell>
</template>
