<script setup lang="ts">
  import HeadingSmall from '@/components/common/HeadingSmall.vue';
  import { useI18n } from '@/composables/useI18n';
  import AppLayout from '@/layouts/AppLayout.vue';
  import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
  import { showUserList } from '@/routes';
  import { type BreadcrumbItem } from '@/types';
  import { Head, usePage } from '@inertiajs/vue3';
  import { computed } from 'vue';
  
  const { t } = useI18n();
  const page = usePage();
  const currentWorkspace = computed(() => page.props.currentWorkspace);
  
  const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    {
      title: t('多客服'),
      href: showUserList.url(currentWorkspace.value.slug),
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
  