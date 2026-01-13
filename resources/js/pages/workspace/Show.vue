<script setup lang="ts">
  import { useI18n } from '@/composables/useI18n';
  import { getWorkspaceList } from '@/routes';
  import { BreadcrumbItem } from '@/types';
  import { usePage } from '@inertiajs/vue3';
  import { computed } from 'vue';
  
  const { t } = useI18n();
  const page = usePage();
  const currentWorkspace = computed(() => page.props.currentWorkspace);
  const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    {
      title: t('工作区管理'),
      href: getWorkspaceList.url(currentWorkspace.value.slug),
    },
  ]);
    
  </script> 
  <template>
    <AppLayout :breadcrumbs="breadcrumbItems">
      
    </AppLayout>
  </template>
    