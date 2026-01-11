<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useI18n } from '@/composables/useI18n';
import { toUrl, urlIsActive } from '@/lib/utils';
import { dashboard } from '@/routes/workspace';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface SubMenuItem {
  title: string;
  href: string | { url: string };
}

interface MenuItem {
  title: string;
  href?: string | { url: string };
  children?: SubMenuItem[];
}

const { t } = useI18n();
const page = usePage();
const currentWorkspace = computed(() => page.props.currentWorkspace);

const sidebarNavItems = computed<MenuItem[]>(() => {
  return [
    {
      title: t('人工接待'),
      children: [
        {
          title: t('我负责的'),
          href: dashboard(currentWorkspace.value.slug),
        },
        {
          title: t('提到我的'),
          href: '#',
        },
        {
          title: t('排队中'),
          href: '#',
        },
        {
          title: t('全部'),
          href: '#',
        },
      ],
    },
    {
      title: t('AI智能体'),
      children: [
        {
          title: t('需人工介入'),
          href: '#',
        },
        {
          title: t('AI接管中'),
          href: '#',
        },
        {
          title: t('全部'),
          href: '#',
        },
      ],
    },
    {
      title: t('渠道'),
      children: [
        {
          title: t('网站（网站1）'),
          href: '#',
        },
        {
          title: t('网站（网站2）'),
          href: '#',
        },
      ],
    },
    {
      title: t('同事的会话'),
      children: [
        {
          title: t('求助'),
          href: '#',
        },
        {
          title: t('同事1'),
          href: '#',
        },
        {
          title: t('同事2'),
          href: '#',
        },
        {
          title: t('同事3'),
          href: '#',
        },
      ],
    },
  ];
});

const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
</script>

<template>
  <div class="flex flex-1 flex-col lg:flex-row">
    <aside class="w-full lg:w-50 lg:self-stretch">
      <nav
        class="flex h-full flex-col space-y-3 border-r border-border/40 bg-card/50 p-4 shadow-sm backdrop-blur-sm"
      >
          <div class="space-y-0.5">
            <h2 class="text-xl font-semibold tracking-tight">
              {{ t('工作台') }}
            </h2>
            <p class="text-sm text-muted-foreground">
              {{ t('查看和处理会话') }}
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
                <Link
                  :href="typeof item.href === 'string' ? item.href : item.href"
                >
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
