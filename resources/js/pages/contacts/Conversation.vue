<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import ContactsLayout from '@/layouts/ContactsLayout.vue';
import contact from '@/routes/contact';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const page = usePage();
const currentWorkspace = computed(() => page.props.currentWorkspace);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('会话记录'),
    href: contact.conversations.url(currentWorkspace.value.slug),
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
