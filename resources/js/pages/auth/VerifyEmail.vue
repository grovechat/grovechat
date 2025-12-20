<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useI18n } from '@/composables/useI18n';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
  status?: string;
}>();

const { t } = useI18n();
</script>

<template>
  <AuthLayout
    :title="t('auth.verifyEmail.title')"
    :description="t('auth.verifyEmail.description')"
  >
    <Head :title="t('auth.verifyEmail.pageTitle')" />

    <div
      v-if="status === 'verification-link-sent'"
      class="mb-4 text-center text-sm font-medium text-green-600"
    >
      {{ t('auth.verifyEmail.linkSent') }}
    </div>

    <Form
      v-bind="send.form()"
      class="space-y-6 text-center"
      v-slot="{ processing }"
    >
      <Button :disabled="processing" variant="secondary">
        <Spinner v-if="processing" />
        {{ t('auth.verifyEmail.submit') }}
      </Button>

      <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
        {{ t('auth.verifyEmail.logout') }}
      </TextLink>
    </Form>
  </AuthLayout>
</template>
