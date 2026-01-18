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
import { createTeammate, showTeammateList } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import type { ShowCreateTeammatePagePropsData, WorkspaceRole } from '@/types/generated';
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const { t } = useI18n();
const props = defineProps<ShowCreateTeammatePagePropsData>();
const currentWorkspace = useRequiredWorkspace();

const roleValue = ref<WorkspaceRole>('operator');
const passwordVisible = ref(false);
const passwordConfirmationVisible = ref(false);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('多客服'),
    href: showTeammateList.url(currentWorkspace.value.slug),
  },
  {
    title: t('新增客服'),
    href: '#',
  },
]);

const roleOptions = computed<{ value: WorkspaceRole; label: string }[]>(() =>
  props.role_options.map((opt) => ({
    value: opt.value as WorkspaceRole,
    label: opt.label,
  })),
);

watch(
  roleOptions,
  (opts) => {
    if (!roleValue.value && opts?.length) {
      roleValue.value = opts[0].value;
    }
  },
  { immediate: true },
);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('新增客服')" />

    <WorkspaceSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('新增客服')"
          :description="t('创建一个新的客服账号并分配角色')"
        />

        <Form
          v-bind="createTeammate.form(currentWorkspace.slug)"
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
              :placeholder="t('请输入客服名称')"
            />
            <InputError class="mt-2" :message="errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="nickname">{{ t('对外昵称') }}</Label>
            <Input
              id="nickname"
              name="nickname"
              class="mt-1 block w-full"
              :placeholder="t('请输入对外昵称')"
            />
            <InputError class="mt-2" :message="errors.nickname" />
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
    </WorkspaceSettingsLayout>
  </AppLayout>
</template>
