<script setup lang="ts">
import UploadImageAction from '@/actions/App/Actions/Attachment/UploadImageAction';
import HeadingSmall from '@/components/common/HeadingSmall.vue';
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
import { showUserList, updateUser } from '@/routes';
import type { AppPageProps, BreadcrumbItem } from '@/types';
import type { UserEditPagePropsData, WorkspaceRole } from '@/types/generated';
import { Form, Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Eye, EyeOff } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const { t } = useI18n();
const page = usePage<AppPageProps<UserEditPagePropsData>>();
const currentWorkspace = useRequiredWorkspace();
const userForm = computed(() => page.props.user_form);

const roleValue = ref<WorkspaceRole>(userForm.value.role);

const avatarPreview = ref<string>(userForm.value.avatar || '');
const avatarUrl = ref<string>(userForm.value.avatar || '');
const uploading = ref(false);
const selectedAvatarFileName = ref<string>('');
const passwordVisible = ref(false);
const passwordConfirmationVisible = ref(false);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('多客服'),
    href: showUserList.url(currentWorkspace.value.slug),
  },
  {
    title: t('编辑客服'),
    href: '#',
  },
]);

const roleOptions = computed<{ value: WorkspaceRole; label: string }[]>(() =>
  page.props.role_options.map((opt) => ({
    value: opt.value as WorkspaceRole,
    label: opt.label,
  })),
);

watch(
  () => roleOptions.value,
  (opts) => {
    if (!roleValue.value && opts?.length) {
      roleValue.value = opts[0].value;
    }
  },
  { immediate: true },
);

const handleAvatarChange = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (!file) return;

  selectedAvatarFileName.value = file.name;

  const reader = new FileReader();
  reader.onload = (e) => {
    avatarPreview.value = e.target?.result as string;
  };
  reader.readAsDataURL(file);

  const formData = new FormData();
  formData.append('file', file);
  formData.append('folder', 'avatars');

  try {
    uploading.value = true;
    const response = await axios.post(UploadImageAction.url(), formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    avatarUrl.value = response.data.full_url;
  } catch {
    avatarPreview.value = userForm.value.avatar || '';
    avatarUrl.value = userForm.value.avatar || '';
    selectedAvatarFileName.value = '';
  } finally {
    uploading.value = false;
  }
};
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
            updateUser.form({
              slug: currentWorkspace.slug,
              id: userForm.id,
            })
          "
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <Label for="name">{{ t('客服名称') }}</Label>
            <Input
              id="name"
              name="name"
              class="mt-1 block w-full"
              required
              :default-value="userForm.name || ''"
              :placeholder="t('请输入客服名称')"
            />
            <InputError class="mt-2" :message="errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="external_nickname">{{ t('对外昵称') }}</Label>
            <Input
              id="external_nickname"
              name="external_nickname"
              class="mt-1 block w-full"
              :default-value="userForm.external_nickname || ''"
              :placeholder="t('请输入对外昵称')"
            />
            <InputError class="mt-2" :message="errors.external_nickname" />
          </div>

          <div class="grid gap-2">
            <Label for="avatar">{{ t('头像') }}</Label>
            <div class="mt-1 space-y-3">
              <div
                v-if="avatarPreview"
                class="relative flex h-20 w-20 items-center justify-center overflow-hidden rounded-full border bg-gray-50"
              >
                <img
                  :src="avatarPreview"
                  :alt="t('头像预览')"
                  class="h-full w-full object-cover"
                />
                <div
                  v-if="uploading"
                  class="absolute inset-0 flex items-center justify-center bg-black/50"
                >
                  <span class="text-sm text-white">{{ t('上传中...') }}</span>
                </div>
              </div>

              <input
                id="avatar"
                name="avatar"
                type="hidden"
                :value="avatarUrl"
              />

              <div class="flex items-center gap-3">
                <input
                  id="avatarFile"
                  type="file"
                  accept="image/*"
                  class="sr-only"
                  :disabled="uploading"
                  @change="handleAvatarChange"
                />
                <Button as-child variant="outline" :disabled="uploading">
                  <Label for="avatarFile" class="cursor-pointer">
                    {{ t('选择图片') }}
                  </Label>
                </Button>
                <span class="text-sm text-muted-foreground">
                  {{ selectedAvatarFileName || t('未选择任何文件') }}
                </span>
              </div>
              <p class="text-sm text-muted-foreground">
                {{ t('支持上传图片格式文件，选择后自动上传') }}
              </p>
            </div>
            <InputError class="mt-2" :message="errors.avatar" />
          </div>

          <div class="grid gap-2">
            <Label for="role">{{ t('角色') }}</Label>
            <input type="hidden" name="role" :value="roleValue" />
            <Select v-model="roleValue">
              <SelectTrigger id="role" class="mt-1">
                <SelectValue :placeholder="t('请选择角色')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="opt in roleOptions"
                  :key="String(opt.value)"
                  :value="String(opt.value)"
                >
                  {{ opt.label }}
                </SelectItem>
              </SelectContent>
            </Select>
            <InputError class="mt-2" :message="errors.role" />
          </div>

          <div class="grid gap-2">
            <Label for="email">{{ t('邮箱') }}</Label>
            <Input
              id="email"
              name="email"
              type="email"
              class="mt-1 block w-full"
              required
              :default-value="userForm.email || ''"
              :placeholder="t('请输入邮箱')"
            />
            <InputError class="mt-2" :message="errors.email" />
          </div>

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
