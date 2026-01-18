<script setup lang="ts">
import UploadImageAction from '@/actions/App/Actions/Attachment/UploadImageAction';
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import ImageUploadField from '@/components/common/ImageUploadField.vue';
import InputError from '@/components/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { useI18n } from '@/composables/useI18n';
import { useRequiredWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import { showTeammateList, updateTeammate } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import type { EditTeammatePagePropsData } from '@/types/generated';
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const { t } = useI18n();
const props = defineProps<EditTeammatePagePropsData>();
const currentWorkspace = useRequiredWorkspace();

const passwordVisible = ref(false);
const passwordConfirmationVisible = ref(false);
const roleValue = ref<string>(String(props.user_form.role ?? ''));

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('多客服'),
    href: showTeammateList.url(currentWorkspace.value.slug),
  },
  {
    title: t('编辑客服'),
    href: '#',
  },
]);

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('编辑客服')" />

    <WorkspaceSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('编辑客服')"
          :description="t('更新客服资料，密码可选不填表示不修改')"
        />

        <Form
          v-bind="
            updateTeammate.form({
              slug: currentWorkspace.slug,
              id: props.user_form.id,
            })
          "
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <Label for="name">{{ t('客服名称') }}</Label>
            <template v-if="props.can_update_profile">
              <Input
                id="name"
                name="name"
                class="mt-1 block w-full"
                required
                :default-value="props.user_form.name || ''"
                :placeholder="t('请输入客服名称')"
              />
            </template>
            <template v-else>
              <input
                type="hidden"
                name="name"
                :value="props.user_form.name || ''"
              />
              <Input
                id="name"
                class="mt-1 block w-full"
                disabled
                :default-value="props.user_form.name || ''"
              />
            </template>
            <InputError class="mt-2" :message="errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="nickname">{{ t('对外昵称') }}</Label>
            <template v-if="props.can_update_profile">
              <Input
                id="nickname"
                name="nickname"
                class="mt-1 block w-full"
                :default-value="props.user_form.nickname || ''"
                :placeholder="t('请输入对外昵称')"
              />
            </template>
            <template v-else>
              <input
                type="hidden"
                name="nickname"
                :value="props.user_form.nickname || ''"
              />
              <Input
                id="nickname"
                class="mt-1 block w-full"
                disabled
                :default-value="props.user_form.nickname || ''"
              />
            </template>
            <InputError class="mt-2" :message="errors.nickname" />
          </div>

          <ImageUploadField
            :label="t('头像')"
            name="avatar"
            :upload-url="UploadImageAction.url()"
            response-key="full_url"
            folder="avatars"
            :initial-preview="props.user_form.avatar || ''"
            :initial-value="props.user_form.avatar || ''"
            :disabled="!props.can_update_profile"
            variant="avatar"
            :error="errors.avatar"
          />

          <div class="grid gap-2">
            <Label for="role">{{ t('角色') }}</Label>
            <template v-if="props.can_update_role">
              <input type="hidden" name="role" :value="roleValue" />
              <Select v-model="roleValue">
                <SelectTrigger id="role" class="mt-1">
                  <SelectValue :placeholder="t('请选择角色')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="opt in props.role_options"
                    :key="String(opt.value)"
                    :value="String(opt.value)"
                  >
                    {{ opt.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </template>
            <template v-else>
              <input
                type="hidden"
                name="role"
                :value="String(props.user_form.role)"
              />
              <Input
                id="role"
                class="mt-1"
                :default-value="props.user_form.role_label"
                disabled
              />
            </template>
            <InputError class="mt-2" :message="errors.role" />
          </div>

          <div class="grid gap-2">
            <Label for="email">{{ t('邮箱') }}</Label>
            <template v-if="props.can_update_email">
              <Input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                required
                :default-value="props.user_form.email || ''"
                :placeholder="t('请输入邮箱')"
              />
            </template>
            <template v-else>
              <input
                type="hidden"
                name="email"
                :value="props.user_form.email || ''"
              />
              <Input
                id="email"
                type="email"
                class="mt-1 block w-full"
                disabled
                :default-value="props.user_form.email || ''"
              />
            </template>
            <InputError class="mt-2" :message="errors.email" />
          </div>

          <template v-if="props.can_update_password">
            <div class="grid gap-2">
              <Label for="password">{{ t('登录密码') }}</Label>
              <div class="relative mt-1">
                <Input
                  id="password"
                  name="password"
                  :type="passwordVisible ? 'text' : 'password'"
                  class="block w-full pr-10"
                  :placeholder="t('不填表示不修改密码')"
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
                  :placeholder="t('如填写密码，请再次输入确认')"
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
          </template>

          <div class="flex items-center gap-4">
            <Button type="submit" :disabled="processing">
              {{ t('保存') }}
            </Button>

            <Transition
              enter-active-class="transition ease-in-out"
              enter-from-class="opacity-0"
              leave-active-class="transition ease-in-out"
              leave-to-class="opacity-0"
            >
              <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                {{ t('保存成功。') }}
              </p>
            </Transition>
          </div>
        </Form>
      </div>
    </WorkspaceSettingsLayout>
  </AppLayout>
</template>
