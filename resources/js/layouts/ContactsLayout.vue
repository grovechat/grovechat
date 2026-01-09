<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useI18n } from '@/composables/useI18n';
import { toUrl, urlIsActive } from '@/lib/utils';
import contact from '@/routes/contact';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const page = usePage();
const currentWorkspace = computed(() => page.props.currentWorkspace);

interface SubMenuItem {
  title: string;
  href: string | { url: string };
}

interface MenuItem {
  title: string;
  href?: string | { url: string };
  children?: SubMenuItem[];
}

const sidebarNavItems = computed<MenuItem[]>(() => {
  return [
    {
      title: t('身份类型'),
      children: [
        {
          title: t('全部'),
          href: contact.index.url({
            slug: currentWorkspace.value.slug,
            type: 'all',
          }),
        },
        {
          title: t('注册用户'),
          href: contact.index.url({
            slug: currentWorkspace.value.slug,
            type: 'customers',
          }),
        },
        {
          title: t('潜在客户'),
          href: contact.index.url({
            slug: currentWorkspace.value.slug,
            type: 'leads',
          }),
        },
      ],
    },
    {
      title: t('会话记录'),
      href: contact.conversations.url(currentWorkspace.value.slug),
    },
  ];
});

const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
</script>

<template>
  <div class="flex flex-1 flex-col lg:flex-row">
    <aside class="w-full lg:w-48 lg:self-stretch">
      <nav
        class="flex h-full flex-col space-y-3 border-r border-border/40 bg-card/50 p-4 shadow-sm backdrop-blur-sm"
      >
          <div class="space-y-0.5">
            <h2 class="text-xl font-semibold tracking-tight">
              {{ t('联系人') }}
            </h2>
            <p class="text-sm text-muted-foreground">
              {{ t('管理您的联系人和会话') }}
            </p>
          </div>

          <div class="flex flex-col space-y-1">
            <template v-for="item in sidebarNavItems" :key="item.title">
              <!-- 有子菜单的分组 -->
              <div v-if="item.children" class="space-y-1">
                <div class="px-2 py-2 text-sm font-semibold text-foreground">
                  {{ item.title }}
                </div>
                <Button
                  v-for="child in item.children"
                  :key="toUrl(child.href)"
                  variant="ghost"
                  :class="[
                    'w-full justify-start pl-6 text-sm font-normal',
                    { 'bg-muted': urlIsActive(child.href, currentPath) },
                  ]"
                  as-child
                >
                  <Link :href="typeof child.href === 'string' ? child.href : child.href">
                    {{ child.title }}
                  </Link>
                </Button>
              </div>

              <!-- 无子菜单的单项 -->
              <Button
                v-else
                variant="ghost"
                :class="[
                  'w-full justify-start',
                  {
                    'bg-muted':
                      item.href && urlIsActive(item.href, currentPath),
                  },
                ]"
                as-child
              >
                <Link :href="typeof item.href === 'string' ? item.href : item.href">
                  {{ item.title }}
                </Link>
              </Button>
            </template>
          </div>
      </nav>
    </aside>

    <Separator class="my-6 lg:hidden" />

    <div class="flex-1 px-4 py-6">
      <section class="max-w-2xl space-y-12">
        <slot />
      </section>
    </div>
  </div>
</template>
