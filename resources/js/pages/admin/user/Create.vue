<script setup lang="ts">
import UploadImageAction from '@/actions/App/Actions/Attachment/UploadImageAction';
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import ImageUploadField from '@/components/common/ImageUploadField.vue';
import InputError from '@/components/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import admin from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import { Form, Head, Link } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const { t } = useI18n();

const passwordVisible = ref(false);
const passwordConfirmationVisible = ref(false);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  { title: t('用户管理'), href: admin.getUserList.url() },
  { title: t('新增用户'), href: admin.showCreateUserPage.url() },
]);
</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('新增用户')" />

    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
        <div class="space-y-6">
          <HeadingSmall :title="t('新增用户')" />

          <Form
            v-bind="admin.createUser.form()"
            class="space-y-6"
            v-slot="{ errors, processing, recentlySuccessful }"
          >
            <div class="grid gap-2">
              <Label for="name">{{ t('名称') }}</Label>
              <Input
                id="name"
                name="name"
                class="mt-1 block w-full"
                required
                :placeholder="t('请输入名称')"
              />
              <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
              <Label for="email">{{ t('邮箱') }}</Label>
              <Input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                required
                :placeholder="t('请输入邮箱')"
              />
              <InputError class="mt-2" :message="errors.email" />
            </div>

            <ImageUploadField
              :label="t('头像')"
              name="avatar"
              :upload-url="UploadImageAction.url()"
              response-key="full_url"
              folder="avatars"
              :initial-preview="''"
              :initial-value="''"
              variant="avatar"
              :error="errors.avatar"
            />

            <div class="grid gap-2">
              <Label for="password">{{ t('登录密码') }}</Label>
              <div class="relative mt-1">
                <Input
                  id="password"
                  name="password"
                  :type="passwordVisible ? 'text' : 'password'"
                  class="block w-full pr-10"
                  required
                  :placeholder="t('请输入登录密码')"
                />
                <button
                  type="button"
                  class="absolute top-1/2 right-2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                  @click="passwordVisible = !passwordVisible"
                >
                  <EyeOff v-if="passwordVisible" class="h-4 w-4" />
                  <Eye v-else class="h-4 w-4" />
                </button>
              </div>
              <InputError class="mt-2" :message="errors.password" />
            </div>

            <div class="grid gap-2">
              <Label for="password_confirmation">{{ t('确认密码') }}</Label>
              <div class="relative mt-1">
                <Input
                  id="password_confirmation"
                  name="password_confirmation"
                  :type="passwordConfirmationVisible ? 'text' : 'password'"
                  class="block w-full pr-10"
                  required
                  :placeholder="t('请再次输入登录密码')"
                />
                <button
                  type="button"
                  class="absolute top-1/2 right-2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                  @click="
                    passwordConfirmationVisible = !passwordConfirmationVisible
                  "
                >
                  <EyeOff v-if="passwordConfirmationVisible" class="h-4 w-4" />
                  <Eye v-else class="h-4 w-4" />
                </button>
              </div>
            </div>

            <div class="flex items-center gap-4">
              <Button type="submit" :disabled="processing">
                {{ t('创建') }}
              </Button>

              <Button variant="outline" as-child :disabled="processing">
                <Link :href="admin.getUserList.url()">{{ t('返回') }}</Link>
              </Button>

              <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
              >
                <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                  {{ t('创建成功。') }}
                </p>
              </Transition>
            </div>
          </Form>
        </div>
      </div>
    </div>
  </SystemAppLayout>
</template>
