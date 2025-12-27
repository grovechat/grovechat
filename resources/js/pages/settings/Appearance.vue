<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/appearance';

const { t } = useI18n();
const { tenantPath } = useTenant();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('appearance.title'),
    href: tenantPath.value ? edit(tenantPath.value).url : '#',
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('appearance.title')" />

    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('appearance.heading')"
          :description="t('appearance.description')"
        />
        <AppearanceTabs />
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
