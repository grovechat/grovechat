<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/InputError.vue';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { edit } from '@/routes/user-password';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';

const { t } = useI18n();
const page = usePage();
const currentWorkspace = computed(() => page.props.currentWorkspace);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('密码设置'),
    href: edit(currentWorkspace.value.slug).url,
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('密码设置')" />

    <SettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('修改密码')"
          :description="t('确保你的账户使用长且随机的密码以保证安全')"
        />

        <Form v-bind="PasswordController.update.form(currentWorkspace.slug)" :options="{preserveScroll: true}"
          reset-on-success
          :reset-on-error="[
            'password',
            'password_confirmation',
            'current_password',
          ]"
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
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
  </AppLayout>
</template>
