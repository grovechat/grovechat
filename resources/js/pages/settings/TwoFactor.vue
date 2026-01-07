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
import SettingsLayout from '@/layouts/SettingsLayout.vue';
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
    title: t('两步验证'),
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
    <Head :title="t('两步验证')" />
    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('两步验证')"
          :description="t('管理你的两步验证设置')"
        />

        <div
          v-if="!twoFactorEnabled"
          class="flex flex-col items-start justify-start space-y-4"
        >
          <Badge variant="destructive">{{ t('已禁用') }}</Badge>

          <p class="text-muted-foreground">
            {{
              t(
                '启用两步验证后，登录时将需要输入安全验证码。该验证码可以通过手机上支持 TOTP 的应用程序获取。',
              )
            }}
          </p>

          <div>
            <Button v-if="hasSetupData" @click="showSetupModal = true">
              <ShieldCheck />{{ t('继续设置') }}
            </Button>
            <Form
              v-else
              v-bind="tenantPath ? enable.form(tenantPath) : {}"
              @success="showSetupModal = true"
              #default="{ processing }"
            >
              <Button type="submit" :disabled="processing">
                <ShieldCheck />{{ t('启用两步验证') }}</Button
              ></Form
            >
          </div>
        </div>

        <div v-else class="flex flex-col items-start justify-start space-y-4">
          <Badge variant="default">{{ t('已启用') }}</Badge>

          <p class="text-muted-foreground">
            {{
              t(
                '启用两步验证后，登录时将需要输入安全的随机验证码，你可以通过手机上支持 TOTP 的应用程序获取该验证码。',
              )
            }}
          </p>

          <TwoFactorRecoveryCodes />

          <div class="relative inline">
            <Form
              v-bind="tenantPath ? disable.form(tenantPath) : {}"
              #default="{ processing }"
            >
              <Button
                variant="destructive"
                type="submit"
                :disabled="processing"
              >
                <ShieldBan />
                {{ t('禁用两步验证') }}
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
