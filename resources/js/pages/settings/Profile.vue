<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import InputError from '@/components/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import { useCurrentWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
  mustVerifyEmail: boolean;
  status?: string;
}

defineProps<Props>();

const { t } = useI18n();
const page = usePage();
const currentWorkspace = useCurrentWorkspace();
const RootLayout = computed(() =>
  page.props.auth.user.is_super_admin ? SystemAppLayout : AppLayout,
);
const linkOptions = computed(() => ({
  mergeQuery: {
    from_workspace: currentWorkspace.value?.slug ?? '',
  },
}));
const user = page.props.auth.user;

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('个人资料设置'),
    href: edit.url(linkOptions.value),
  },
]);
</script>

<template>
  <component :is="RootLayout" :breadcrumbs="breadcrumbItems">
    <Head :title="t('个人资料设置')" />

    <SettingsLayout>
      <div class="flex flex-col space-y-6">
        <HeadingSmall
          :title="t('个人信息')"
          :description="t('更新你的姓名和电子邮件地址')"
        />

        <Form
          v-bind="ProfileController.update.form(linkOptions)"
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <Label for="name">{{ t('姓名') }}</Label>
            <Input
              id="name"
              class="mt-1 block w-full"
              name="name"
              :default-value="user.name"
              required
              autocomplete="name"
              :placeholder="t('请输入姓名')"
            />
            <InputError class="mt-2" :message="errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="email">{{ t('电子邮件地址') }}</Label>
            <Input
              id="email"
              type="email"
              class="mt-1 block w-full"
              name="email"
              :default-value="user.email"
              required
              autocomplete="username"
              :placeholder="t('请输入电子邮件地址')"
            />
            <InputError class="mt-2" :message="errors.email" />
          </div>

          <div v-if="mustVerifyEmail && !user.email_verified_at">
            <p class="-mt-4 text-sm text-muted-foreground">
              {{ t('你的电子邮件地址未验证。') }}
              <Link
                :href="send.url(linkOptions)"
                as="button"
                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
              >
                {{ t('点击这里重新发送验证邮件。') }}
              </Link>
            </p>

            <div
              v-if="status === 'verification-link-sent'"
              class="mt-2 text-sm font-medium text-green-600"
            >
              {{ t('新的验证链接已发送到你的电子邮件地址。') }}
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Button :disabled="processing" data-test="update-profile-button">{{
              t('保存')
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
