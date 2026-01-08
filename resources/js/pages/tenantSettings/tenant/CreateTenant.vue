<script setup lang="ts">
import CommonController from '@/actions/App/Http/Controllers/Api/CommonController';
import TenantSettingController from '@/actions/App/Http/Controllers/TenantSettingController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import AppLayout from '@/layouts/AppLayout.vue';
import TenantSettingsLayout from '@/layouts/TenantSettingsLayout.vue';
import tenantSetting from '@/routes/tenant-setting';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Check, Copy } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const { t } = useI18n();
const { tenantPath } = useTenant();
const page = usePage();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('创建工作区'),
    href: '#',
  },
]);

const logoPreview = ref<string>('');
const logoUrl = ref<string>('');
const uploading = ref(false);
const pathInput = ref<string>('');
const copied = ref(false);

// 从共享数据中获取 generalSettings
const generalSettings = computed(() => page.props.generalSettings as any);

// 计算完整的访问路径
const fullAccessUrl = computed(() => {
  const baseUrl = generalSettings.value?.baseUrl || '';
  return `${baseUrl}/w/${pathInput.value}`;
});

const handleLogoChange = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (!file) return;

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
    const response = await axios.post(CommonController.uploadImage.url(), formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    // 上传成功后更新logo URL
    logoUrl.value = response.data.url;
  } catch {
    // 上传失败，恢复原来的logo
    logoPreview.value = '';
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

    <TenantSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('创建工作区')"
          :description="t('创建一个新的工作区来组织你的团队和项目')"
        />

        <Form
          v-bind="
            tenantPath
              ? TenantSettingController.storeTenant.form(tenantPath)
              : {}
          "
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
            <Label for="logo">{{ t('Logo') }}</Label>
            <div class="mt-1 space-y-3">
              <div
                v-if="logoPreview"
                class="w-32 h-32 border rounded-md overflow-hidden bg-gray-50 flex items-center justify-center relative"
              >
                <img
                  :src="logoPreview"
                  alt="Logo预览"
                  class="max-w-full max-h-full object-contain"
                />
                <div
                  v-if="uploading"
                  class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center"
                >
                  <span class="text-white text-sm">{{ t('上传中...') }}</span>
                </div>
              </div>
              <input
                id="logo"
                name="logo"
                type="hidden"
                :value="logoUrl"
              />
              <Input
                id="logoFile"
                type="file"
                accept="image/*"
                class="block w-full"
                :disabled="uploading"
                @change="handleLogoChange"
              />
              <p class="text-sm text-muted-foreground">
                {{ t('支持上传图片格式文件，选择后自动上传') }}
              </p>
            </div>
            <InputError class="mt-2" :message="errors.logo" />
          </div>

          <div class="grid gap-2">
            <Label for="path">{{ t('访问路径 (path)') }}</Label>
            <Input
              id="path"
              name="path"
              class="mt-1 block w-full"
              v-model="pathInput"
              required
              :placeholder="t('请输入访问路径')"
            />
            <div class="flex items-center gap-1.5 mt-1">
              <p class="text-sm text-muted-foreground">
                {{ fullAccessUrl }}
              </p>
              <Button
                type="button"
                variant="ghost"
                size="sm"
                @click="copyToClipboard"
                class="shrink-0 h-6 px-2"
              >
                <Check v-if="copied" class="h-3.5 w-3.5" />
                <Copy v-else class="h-3.5 w-3.5" />
              </Button>
            </div>
            <InputError class="mt-2" :message="errors.path" />
          </div>

          <div class="flex items-center gap-4">
            <Button
              type="submit"
              :disabled="processing"
              data-test="create-tenant-button"
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
    </TenantSettingsLayout>
  </AppLayout>
</template>
