<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import { useWorkspace } from '@/composables/useWorkspace';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { edit } from '@/routes/appearance';

const { t } = useI18n();
const { workspaceSlug } = useWorkspace();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('外观设置'),
    href: workspaceSlug.value ? edit(workspaceSlug.value).url : '#',
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('外观设置')" />

    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('外观设置')"
          :description="t('更新你账户的外观设置')"
        />
        <AppearanceTabs />
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
