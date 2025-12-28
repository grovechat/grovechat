<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import AppLayout from '@/layouts/AppLayout.vue';
import ContactsLayout from '@/layouts/ContactsLayout.vue';
import contact from '@/routes/contact';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { tenantPath } = useTenant();
const page = usePage();

// 从路由参数获取类型
const type = computed(() => page.url.split('/').find((segment, index, arr) => 
  arr[index - 1] === 'contacts' && ['all', 'customers', 'leads'].includes(segment)
) || 'all');

const typeLabels: Record<string, string> = {
  all: t('全部'),
  customers: t('注册用户'),
  leads: t('潜在客户'),
};

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: typeLabels[type.value],
    href: tenantPath.value ? contact.index.url({ tenant_path: tenantPath.value, type: type.value }) : '#',
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="typeLabels[type]" />

    <ContactsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="typeLabels[type]"
          :description="t('查看和管理联系人信息')"
        />
      </div>
    </ContactsLayout>
  </AppLayout>
</template>
