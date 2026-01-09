<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import { useWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import SystemSettingsLayout from '@/layouts/SystemSettingsLayout.vue';
import systemSetting from '@/routes/system-setting';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { workspacePath } = useWorkspace();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('安全'),
    href: workspacePath.value
      ? systemSetting.getSecuritySettings.url(workspacePath.value)
      : '#',
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('安全')" />

    <SystemSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('安全')"
          :description="
            t(
              '这里是安全设置页面的内容。您可以在这里配置安全策略、访问控制等选项。',
            )
          "
        />
      </div>
    </SystemSettingsLayout>
  </AppLayout>
</template>
