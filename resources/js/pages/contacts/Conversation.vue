<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import { useWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import ContactsLayout from '@/layouts/ContactsLayout.vue';
import contact from '@/routes/contact';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { workspaceSlug } = useWorkspace();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('会话记录'),
    href: workspaceSlug.value ? contact.conversations.url(workspaceSlug.value) : '#',
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('会话记录')" />

    <ContactsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('会话记录')"
          :description="t('查看所有联系人的会话历史')"
        />
      </div>
    </ContactsLayout>
  </AppLayout>
</template>
