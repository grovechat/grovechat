<script setup lang="ts">
import CommonController from '@/actions/App/Http/Controllers/Api/CommonController';
import SystemSettingController from '@/actions/App/Http/Controllers/SystemSettingController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import { useWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import SystemSettingsLayout from '@/layouts/SystemSettingsLayout.vue';
import systemSetting from '@/routes/system-setting';
import { type BreadcrumbItem } from '@/types';
import { type GeneralSettingsData } from '@/types/generated';
import { Form, Head } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

const props = defineProps<GeneralSettingsData>();

const { t } = useI18n();
const { workspacePath } = useWorkspace();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('基础设置'),
    href: workspacePath.value
      ? systemSetting.getGeneralSettings.url(workspacePath.value)
      : '#',
  },
]);

const logoPreview = ref<string>(props.logo || '');
const logoUrl = ref<string>(props.logo || '');
const uploading = ref(false);

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
    logoPreview.value = props.logo || '';
  } finally {
    uploading.value = false;
  }
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('基础设置')" />

    <SystemSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('基础设置')"
          :description="t('配置系统的基本信息和全局设置')"
        />

        <Form
          v-bind="
            workspacePath
              ? SystemSettingController.updateGeneralSettings.form(workspacePath)
              : {}
          "
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <Label for="baseUrl">{{ t('主机地址') }}</Label>
            <Input
              id="baseUrl"
              name="baseUrl"
              type="url"
              class="mt-1 block w-full"
              :default-value="baseUrl || undefined"
              required
              :placeholder="t('请输入主机地址，例如：https://example.com')"
            />
            <InputError class="mt-2" :message="errors.baseUrl" />
          </div>

          <div class="grid gap-2">
            <Label for="name">{{ t('系统名称') }}</Label>
            <Input
              id="name"
              name="name"
              class="mt-1 block w-full"
              :default-value="props.name || undefined"
              required
              :placeholder="t('请输入系统名称')"
            />
            <InputError class="mt-2" :message="errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="logo">{{ t('系统Logo') }}</Label>
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
            <Label for="copyright">{{ t('版权信息') }}</Label>
            <Input
              id="copyright"
              name="copyright"
              class="mt-1 block w-full"
              :default-value="copyright || undefined"
              :placeholder="t('请输入版权信息')"
            />
            <InputError class="mt-2" :message="errors.copyright" />
          </div>

          <div class="grid gap-2">
            <Label for="icpRecord">{{ t('备案信息') }}</Label>
            <Input
              id="icpRecord"
              name="icpRecord"
              class="mt-1 block w-full"
              :default-value="icpRecord || undefined"
              :placeholder="t('请输入备案信息')"
            />
            <InputError class="mt-2" :message="errors.icpRecord" />
          </div>

          <div class="grid gap-2">
            <Label>{{ t('版本号') }}</Label>
            <div class="text-sm text-muted-foreground py-2">
              {{ version || t('未设置') }}
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Button
              type="submit"
              :disabled="processing"
              data-test="update-general-settings-button"
            >
              {{ t('保存') }}
            </Button>

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
    </SystemSettingsLayout>
  </AppLayout>
</template>
