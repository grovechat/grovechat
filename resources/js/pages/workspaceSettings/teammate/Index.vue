<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import { useWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import workspaceSetting from '@/routes/workspace-setting';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { workspacePath } = useWorkspace();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('多客服'),
    href: workspacePath.value
      ? workspaceSetting.teammate.index.url(workspacePath.value)
      : '#',
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('多客服')" />

    <WorkspaceSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('多客服')"
          :description="t('管理团队成员和客服人员')"
        />
      </div>
    </WorkspaceSettingsLayout>
  </AppLayout>
</template>
