<script setup lang="ts">
import fallbackLogoUrl from '@/assets/images/logo.png';
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbPage,
  BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { Button } from '@/components/ui/button';
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
} from '@/components/ui/sidebar';
import Toaster from '@/components/ui/toast/Toaster.vue';
import { useErrorHandling } from '@/composables/useToast';
import SidebarContextConsumer from '@/layouts/app/SidebarContextConsumer.vue';
import SidebarUserMenu from '@/layouts/app/SidebarUserMenu.vue';
import { cn, toUrl, urlIsActive } from '@/lib/utils';
import type { BreadcrumbItemType, NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Pin } from 'lucide-vue-next';
import { computed } from 'vue';

export type SidebarShellNavItem = NavItem & {
  activeUrls?: string[];
};

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
  headerHref: string;
  headerSubtitle: string;
  mainNavItems: SidebarShellNavItem[];
  footerNavItems: NavItem[];
  profileHref: string;
  profileLabel: string;
  logoutHref: string;
}

const props = withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const page = usePage();
const isOpen = Boolean(page.props.sidebarOpen);
useErrorHandling();

const generalSettings = computed(() => page.props.generalSettings);
const systemName = computed(() => generalSettings.value?.name || 'GroveChat');
const systemLogo = computed(
  () => generalSettings.value?.logo_url || fallbackLogoUrl,
);

const isExternalLink = (href: NavItem['href']) => {
  const url = toUrl(href);
  return url.startsWith('http://') || url.startsWith('https://');
};

const isMainNavItemActive = (item: SidebarShellNavItem) => {
  if (item.activeUrls && item.activeUrls.length > 0) {
    return item.activeUrls.some((u) =>
      urlIsActive(u, page.url, { mode: 'prefix' }),
    );
  }

  return urlIsActive(item.href, page.url);
};
</script>

<template>
  <SidebarProvider :default-open="isOpen">
    <Sidebar collapsible="icon" variant="inset">
      <SidebarContextConsumer v-slot="{ toggleSidebar, state, isMobile }">
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
                    :href="props.headerHref"
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

                    <slot name="headerSubtitle">
                      <span class="text-xs text-muted-foreground">
                        {{ props.headerSubtitle }}
                      </span>
                    </slot>
                  </div>

                  <slot name="collapsedHeaderAddon" />
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
                :fill="state.value === 'expanded' ? 'currentColor' : 'none'"
              />
              <span class="sr-only">Toggle Sidebar</span>
            </Button>
          </div>
        </SidebarHeader>

        <SidebarContent>
          <SidebarGroup class="px-2 py-0">
            <SidebarMenu>
              <SidebarMenuItem
                v-for="item in props.mainNavItems"
                :key="item.title"
              >
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
                <SidebarMenuItem
                  v-for="item in props.footerNavItems"
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
              <slot
                name="userMenu"
                :isMobile="isMobile.value"
                :sidebarState="state.value"
              >
                <SidebarUserMenu
                  :profile-href="props.profileHref"
                  :profile-label="props.profileLabel"
                  :logout-href="props.logoutHref"
                  :is-mobile="isMobile.value"
                  :sidebar-state="state.value"
                />
              </slot>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarFooter>
      </SidebarContextConsumer>
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
                <template
                  v-for="(item, index) in props.breadcrumbs"
                  :key="index"
                >
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
