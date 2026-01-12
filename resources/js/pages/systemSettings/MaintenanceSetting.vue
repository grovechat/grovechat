<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import SystemSettingsLayout from '@/layouts/SystemSettingsLayout.vue';
import systemSetting from '@/routes/system-setting';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const page = usePage();
const currentWorkspace = computed(() => page.props.currentWorkspace);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('维护'),
    href: systemSetting.getMaintenanceSettings.url(currentWorkspace.value.slug),
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('维护')" />

    <SystemSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('维护')"
          :description="
            t(
              '这里是维护设置页面的内容。您可以在这里执行系统维护、备份恢复等操作。',
            )
          "
        />
      </div>
    </SystemSettingsLayout>
  </AppLayout>
</template>
