<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import { toUrl, urlIsActive } from '@/lib/utils';
import tenantSetting from '@/routes/tenant-setting';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { tenantPath } = useTenant();

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
  if (!tenantPath.value) return [];

  return [
    {
      title: t('工作区'),
      children: [
        {
          title: t('常规设置'),
          href: tenantSetting.tenant.general.url(tenantPath.value),
        },
      ],
    },
    {
      title: t('客服'),
      children: [
        {
          title: t('多客服'),
          href: tenantSetting.teammate.index.url(tenantPath.value),
        },
      ],
    },
    {
      title: t('渠道'),
      children: [
        {
          title: t('网站'),
          href: tenantSetting.channels.web.url(tenantPath.value),
        },
      ],
    },
    {
      title: t('数据'),
      children: [
        {
          title: t('标签'),
          href: tenantSetting.datas.tag.url(tenantPath.value),
        },
        {
          title: t('自定义属性'),
          href: tenantSetting.datas.attribute.url(tenantPath.value),
        },
      ],
    },
  ];
});

const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';
</script>

<template>
  <div>
    <div class="flex flex-col lg:flex-row lg:items-start">
      <aside class="w-full lg:w-48">
        <nav class="flex flex-col space-y-3 border-r border-border/40 bg-card/50 p-4 shadow-sm backdrop-blur-sm min-h-screen">
          <div class="space-y-0.5">
            <h2 class="text-xl font-semibold tracking-tight">{{ t('管理中心') }}</h2>
            <p class="text-sm text-muted-foreground">{{ t('管理工作区和团队设置') }}</p>
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
                  'w-full justify-start pl-6 font-normal text-sm',
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
                { 'bg-muted': item.href && urlIsActive(item.href, currentPath) },
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
  </div>
</template>
