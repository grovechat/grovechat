<script setup lang="ts">
import CreateWorkspaceAction from '@/actions/App/Actions/Manage/CreateWorkspaceAction';
import CommonController from '@/actions/App/Http/Controllers/Api/CommonController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Check, Copy } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const { t } = useI18n();
const page = usePage();
const generalSettings = computed(() => page.props.generalSettings);
const currentWorkspace = computed(() => page.props.currentWorkspace);
const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('创建工作区'),
    href: '#',
  },
]);
const logoPreview = ref<string>('');
const logoId = ref<string>('');
const uploading = ref(false);
const selectedLogoFileName = ref<string>('');
const slugInput = ref<string>('');
const copied = ref(false);

// 计算完整的访问路径
const fullAccessUrl = computed(() => {
  const baseUrl = generalSettings.value?.base_url || '';
  return `${baseUrl}/w/${slugInput.value}`;
});

const handleLogoChange = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (!file) return;
  selectedLogoFileName.value = file.name;

  // 先显示本地预览
  const reader = new FileReader();
  reader.onload = (e) => {
    logoPreview.value = e.target?.result as string;
  };
  reader.readAsDataURL(file);

  // 上传文件到服务器
  const formData = new FormData();
  formData.append('file', file);

  try {
    uploading.value = true;
    const response = await axios.post(
      CommonController.uploadImage.url(),
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      },
    );
    logoId.value = response.data.id;
  } catch {
    logoPreview.value = '';
    selectedLogoFileName.value = '';
  } finally {
    uploading.value = false;
  }
};

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(fullAccessUrl.value);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('创建工作区')" />

    <WorkspaceSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('创建工作区')"
          :description="t('创建一个新的工作区来组织你的团队和项目')"
        />

        <Form
          v-bind="CreateWorkspaceAction.form(currentWorkspace.slug)"
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <Label for="name">{{ t('工作区名称') }}</Label>
            <Input
              id="name"
              name="name"
              class="mt-1 block w-full"
              required
              :placeholder="t('请输入工作区名称')"
            />
            <InputError class="mt-2" :message="errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="logo_id">{{ t('Logo') }}</Label>
            <div class="mt-1 space-y-3">
              <div
                v-if="logoPreview"
                class="relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-md border bg-gray-50"
              >
                <img
                  :src="logoPreview"
                  alt="Logo预览"
                  class="max-h-full max-w-full object-contain"
                />
                <div
                  v-if="uploading"
                  class="bg-opacity-50 absolute inset-0 flex items-center justify-center bg-black"
                >
                  <span class="text-sm text-white">{{ t('上传中...') }}</span>
                </div>
              </div>
              <input
                id="logo_id"
                name="logo_id"
                type="hidden"
                :value="logoId"
              />
              <div class="flex items-center gap-3">
                <input
                  id="logoFile"
                  type="file"
                  accept="image/*"
                  class="sr-only"
                  :disabled="uploading"
                  @change="handleLogoChange"
                />
                <Button as-child variant="outline" :disabled="uploading">
                  <Label for="logoFile" class="cursor-pointer">
                    {{ t('选择文件') }}
                  </Label>
                </Button>
                <span class="text-sm text-muted-foreground">
                  {{ selectedLogoFileName || t('未选择任何文件') }}
                </span>
              </div>
              <p class="text-sm text-muted-foreground">
                {{ t('支持上传图片格式文件，选择后自动上传') }}
              </p>
            </div>
            <InputError class="mt-2" :message="errors.logo" />
          </div>

          <div class="grid gap-2">
            <Label for="slug">{{ t('访问路径') }}</Label>
            <Input
              id="slug"
              name="slug"
              class="mt-1 block w-full"
              v-model="slugInput"
              required
              :placeholder="t('请输入访问路径')"
            />
            <div class="mt-1 flex items-center gap-1.5">
              <p class="text-sm text-muted-foreground">
                {{ fullAccessUrl }}
              </p>
              <Button
                type="button"
                variant="ghost"
                size="sm"
                @click="copyToClipboard"
                class="h-6 shrink-0 px-2"
              >
                <Check v-if="copied" class="h-3.5 w-3.5" />
                <Copy v-else class="h-3.5 w-3.5" />
              </Button>
            </div>
            <InputError class="mt-2" :message="errors.slug" />
          </div>

          <div class="flex items-center gap-4">
            <Button
              type="submit"
              :disabled="processing"
              data-test="create-workspace-button"
            >
              {{ t('创建工作区') }}
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
