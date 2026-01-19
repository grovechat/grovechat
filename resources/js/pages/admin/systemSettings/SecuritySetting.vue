<script setup lang="ts">
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import admin from '@/routes/admin';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('安全'),
    href: admin.getSecuritySettings.url(),
  },
]);
</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('安全')" />

    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
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
      </div>
    </div>
  </SystemAppLayout>
</template>
