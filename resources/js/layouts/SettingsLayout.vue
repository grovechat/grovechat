<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editLanguage } from '@/routes/language';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { tenantPath } = useTenant();

const sidebarNavItems = computed<NavItem[]>(() => {
  if (!tenantPath.value) return [];

  return [
    {
      title: t('个人资料'),
      href: editProfile(tenantPath.value),
    },
    {
      title: t('密码'),
      href: editPassword(tenantPath.value),
    },
    {
      title: t('两步验证'),
      href: show(tenantPath.value),
    },
    {
      title: t('语言和时区'),
      href: editLanguage(tenantPath.value),
    },
    {
      title: t('外观'),
      href: editAppearance(tenantPath.value),
    },
  ];
});

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
  <div class="flex flex-1 flex-col lg:flex-row">
    <aside class="w-full lg:w-48 lg:self-stretch">
      <nav
        class="flex h-full flex-col space-y-3 border-r border-border/40 bg-card/50 p-4 shadow-sm backdrop-blur-sm"
      >
          <div class="space-y-0.5">
            <h2 class="text-xl font-semibold tracking-tight">
              {{ t('设置') }}
            </h2>
            <p class="text-sm text-muted-foreground">
              {{ t('管理你的个人资料和账户设置') }}
            </p>
          </div>

          <div class="flex flex-col space-y-1">
            <Button
              v-for="item in sidebarNavItems"
              :key="toUrl(item.href)"
              variant="ghost"
              :class="[
                'w-full justify-start',
                { 'bg-muted': urlIsActive(item.href, currentPath) },
              ]"
              as-child
            >
              <Link
                :href="typeof item.href === 'string' ? item.href : item.href"
              >
                <component :is="item.icon" class="h-4 w-4" />
                {{ item.title }}
              </Link>
            </Button>
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
