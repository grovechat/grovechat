<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { disable, enable, show } from '@/routes/two-factor';
import { BreadcrumbItem } from '@/types';
import { Form, Head } from '@inertiajs/vue3';
import { ShieldBan, ShieldCheck } from 'lucide-vue-next';
import { computed, onUnmounted, ref } from 'vue';

interface Props {
  requiresConfirmation?: boolean;
  twoFactorEnabled?: boolean;
}

withDefaults(defineProps<Props>(), {
  requiresConfirmation: false,
  twoFactorEnabled: false,
});

const { t } = useI18n();
const { tenantPath } = useTenant();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
  {
    title: t('twoFactor.title'),
    href: tenantPath.value ? show(tenantPath.value).url : '#',
  },
]);

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => {
  clearTwoFactorAuthData();
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head :title="t('twoFactor.title')" />
    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('twoFactor.heading')"
          :description="t('twoFactor.description')"
        />

        <div
          v-if="!twoFactorEnabled"
          class="flex flex-col items-start justify-start space-y-4"
        >
          <Badge variant="destructive">{{
            t('twoFactor.status.disabled')
          }}</Badge>

          <p class="text-muted-foreground">
            {{ t('twoFactor.disabled.description') }}
          </p>

          <div>
            <Button v-if="hasSetupData" @click="showSetupModal = true">
              <ShieldCheck />{{ t('twoFactor.disabled.continueSetup') }}
            </Button>
            <Form
              v-else
              v-bind="tenantPath ? enable.form(tenantPath) : {}"
              @success="showSetupModal = true"
              #default="{ processing }"
            >
              <Button type="submit" :disabled="processing">
                <ShieldCheck />{{ t('twoFactor.disabled.enable') }}</Button
              ></Form
            >
          </div>
        </div>

        <div v-else class="flex flex-col items-start justify-start space-y-4">
          <Badge variant="default">{{ t('twoFactor.status.enabled') }}</Badge>

          <p class="text-muted-foreground">
            {{ t('twoFactor.enabled.description') }}
          </p>

          <TwoFactorRecoveryCodes />

          <div class="relative inline">
            <Form v-bind="tenantPath ? disable.form(tenantPath) : {}" #default="{ processing }">
              <Button
                variant="destructive"
                type="submit"
                :disabled="processing"
              >
                <ShieldBan />
                {{ t('twoFactor.enabled.disable') }}
              </Button>
            </Form>
          </div>
        </div>

        <TwoFactorSetupModal
          v-model:isOpen="showSetupModal"
          :requiresConfirmation="requiresConfirmation"
          :twoFactorEnabled="twoFactorEnabled"
        />
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
