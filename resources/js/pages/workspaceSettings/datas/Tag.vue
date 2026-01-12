<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import workspaceSetting from '@/routes/workspace-setting';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const page = usePage();
const currentWorkspace = computed(() => page.props.currentWorkspace);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('标签'),
    href: workspaceSetting.datas.tag.url(currentWorkspace.value.slug),
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('标签')" />

    <WorkspaceSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('标签')"
          :description="t('管理和组织联系人标签')"
        />
      </div>
    </WorkspaceSettingsLayout>
  </AppLayout>
</template>
