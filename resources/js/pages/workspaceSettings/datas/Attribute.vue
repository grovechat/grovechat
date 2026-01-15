<script setup lang="ts">
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import { useRequiredWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import workspaceSetting from '@/routes/workspace-setting';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
usePage();
const currentWorkspace = useRequiredWorkspace();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('自定义属性'),
    href: workspaceSetting.datas.attribute.url(currentWorkspace.value.slug),
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('自定义属性')" />

    <WorkspaceSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('自定义属性')"
          :description="t('创建和管理自定义联系人属性')"
        />
      </div>
    </WorkspaceSettingsLayout>
  </AppLayout>
</template>
