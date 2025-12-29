<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import AppLayout from '@/layouts/AppLayout.vue';
import SystemSettingsLayout from '@/layouts/SystemSettingsLayout.vue';
import systemSetting from '@/routes/system-setting';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

interface Props {
  baseUrl: string | null;
  name: string | null;
  logo: string | null;
  copyright: string | null;
  icpRecord: string | null;
  version: string | null;
}

const props = defineProps<Props>();

const { t } = useI18n();
const { tenantPath } = useTenant();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('基础设置'),
    href: tenantPath.value
      ? systemSetting.getGeneralSettings.url(tenantPath.value)
      : '#',
  },
]);

const form = useForm({
  baseUrl: props.baseUrl || '',
  name: props.name || '',
  logo: props.logo || '',
  copyright: props.copyright || '',
  icpRecord: props.icpRecord || '',
});

const logoPreview = ref<string>(props.logo || '');
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

  // 立即上传文件
  const formData = new FormData();
  formData.append('file', file);

  try {
    uploading.value = true;
    const response = await axios.post('/api/upload-image', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    // 上传成功后更新表单数据
    form.logo = response.data.url;
  } catch (error) {
    console.error('Logo upload failed:', error);
    // 上传失败，恢复原来的logo
    logoPreview.value = props.logo || '';
  } finally {
    uploading.value = false;
  }
};

const handleSubmit = () => {
  if (!tenantPath.value) return;

  form.put(systemSetting.updateGeneralSettings.url(tenantPath.value), {
    preserveScroll: true,
  });
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

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="grid gap-2">
            <Label for="baseUrl">{{ t('主机地址') }}</Label>
            <Input
              id="baseUrl"
              v-model="form.baseUrl"
              type="url"
              class="mt-1 block w-full"
              required
              :placeholder="t('请输入主机地址，例如：https://example.com')"
            />
            <InputError class="mt-2" :message="form.errors.baseUrl" />
          </div>

          <div class="grid gap-2">
            <Label for="name">{{ t('系统名称') }}</Label>
            <Input
              id="name"
              v-model="form.name"
              class="mt-1 block w-full"
              required
              :placeholder="t('请输入系统名称')"
            />
            <InputError class="mt-2" :message="form.errors.name" />
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
              <Input
                id="logo"
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
            <InputError class="mt-2" :message="form.errors.logo" />
          </div>

          <div class="grid gap-2">
            <Label for="copyright">{{ t('版权信息') }}</Label>
            <Input
              id="copyright"
              v-model="form.copyright"
              class="mt-1 block w-full"
              :placeholder="t('请输入版权信息')"
            />
            <InputError class="mt-2" :message="form.errors.copyright" />
          </div>

          <div class="grid gap-2">
            <Label for="icpRecord">{{ t('备案信息') }}</Label>
            <Input
              id="icpRecord"
              v-model="form.icpRecord"
              class="mt-1 block w-full"
              :placeholder="t('请输入备案信息')"
            />
            <InputError class="mt-2" :message="form.errors.icpRecord" />
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
              :disabled="form.processing"
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
              <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">
                {{ t('已保存。') }}
              </p>
            </Transition>
          </div>
        </form>
      </div>
    </SystemSettingsLayout>
  </AppLayout>
</template>
