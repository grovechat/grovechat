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
    title: t('自定义属性'),
    href: tenantPath.value ? tenantSetting.datas.attribute.url(tenantPath.value) : '#',
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('自定义属性')" />

    <TenantSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('自定义属性')"
          :description="t('创建和管理自定义联系人属性')"
        />
      </div>
    </TenantSettingsLayout>
  </AppLayout>
</template>
