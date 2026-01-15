<script setup lang="ts">
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import systemSetting from '@/routes/system-setting';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('邮箱服务器'),
    href: systemSetting.getMailSettings.url(),
  },
]);
</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('邮箱服务器')" />

    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
        <div class="space-y-6">
          <HeadingSmall
            :title="t('邮箱服务器')"
            :description="
              t(
                '这里是邮箱服务器设置页面的内容。您可以在这里配置SMTP服务器、邮件发送等选项。',
              )
            "
          />
        </div>
      </div>
    </div>
  </SystemAppLayout>
</template>
