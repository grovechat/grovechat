<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/common/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import { edit } from '@/routes/user-password';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import HeadingSmall from '@/components/common/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

const { t } = useI18n();
const page = usePage();
const fromWorkspaceSlug = computed(() => page.props.fromWorkspaceSlug);
const currentWorkspace = computed(() => page.props.currentWorkspace);
const RootLayout = computed(() => (currentWorkspace.value ? AppLayout : SystemAppLayout));
const linkOptions = computed(() => ({
  mergeQuery: {
    from_workspace: fromWorkspaceSlug.value,
  },
}));

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('密码设置'),
    href: edit.url(linkOptions.value),
  },
]);
</script>

<template>
  <component :is="RootLayout" :breadcrumbs="breadcrumbItems">
    <Head :title="t('密码设置')" />

    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('修改密码')"
          :description="t('确保你的账户使用长且随机的密码以保证安全')"
        />

        <Form
          v-bind="PasswordController.update.form(linkOptions)"
          :options="{ preserveScroll: true }"
          reset-on-success
          :reset-on-error="[
            'password',
            'password_confirmation',
            'current_password',
          ]"
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <!-- Hidden username field for password managers -->
          <input
            type="text"
            name="username"
            :value="page.props.auth.user.email"
            autocomplete="username"
            style="display: none"
            aria-hidden="true"
            tabindex="-1"
          />

          <div class="grid gap-2">
            <Label for="current_password">{{ t('当前密码') }}</Label>
            <Input
              id="current_password"
              name="current_password"
              type="password"
              class="mt-1 block w-full"
              autocomplete="current-password"
              :placeholder="t('请输入当前密码')"
            />
            <InputError :message="errors.current_password" />
          </div>

          <div class="grid gap-2">
            <Label for="password">{{ t('新密码') }}</Label>
            <Input
              id="password"
              name="password"
              type="password"
              class="mt-1 block w-full"
              autocomplete="new-password"
              :placeholder="t('请输入新密码')"
            />
            <InputError :message="errors.password" />
          </div>

          <div class="grid gap-2">
            <Label for="password_confirmation">{{ t('确认密码') }}</Label>
            <Input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              class="mt-1 block w-full"
              autocomplete="new-password"
              :placeholder="t('请再次输入新密码')"
            />
            <InputError :message="errors.password_confirmation" />
          </div>

          <div class="flex items-center gap-4">
            <Button :disabled="processing" data-test="update-password-button">{{
              t('保存密码')
            }}</Button>

            <Transition
              enter-active-class="transition ease-in-out"
              enter-from-class="opacity-0"
              leave-active-class="transition ease-in-out"
              leave-to-class="opacity-0"
            >
              <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                {{ t('已保存。') }}
              </p>
            </Transition>
          </div>
        </Form>
      </div>
    </SettingsLayout>
  </component>
</template>
