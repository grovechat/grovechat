<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { dashboard } from '@/routes/workspace';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const page = usePage();
const currentWorkspace = computed(() => page.props.currentWorkspace);
const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  {
    title: t('我负责的'),
    href: dashboard.url(currentWorkspace.value.slug),
  },
]);
</script>

<template>
  <Head :title="t('工作台')" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <DashboardLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('我负责的')"
          :description="t('查看我负责的会话')"
        />
      </div>
    </DashboardLayout>
  </AppLayout>
</template>
