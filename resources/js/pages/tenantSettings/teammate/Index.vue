<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import AppLayout from '@/layouts/AppLayout.vue';
import TenantSettingsLayout from '@/layouts/TenantSettingsLayout.vue';
import tenantSetting from '@/routes/tenant-setting';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { tenantPath } = useTenant();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('多客服'),
    href: tenantPath.value ? tenantSetting.teammate.index.url(tenantPath.value) : '#',
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('多客服')" />

    <TenantSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('多客服')"
          :description="t('管理团队成员和客服人员')"
        />
      </div>
    </TenantSettingsLayout>
  </AppLayout>
</template>
