<script setup lang="ts">
import SystemSetting from '@/actions/App/Actions/SystemSetting';
import CommonController from '@/actions/App/Http/Controllers/Api/CommonController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import AppLayout from '@/layouts/AppLayout.vue';
import SystemSettingsLayout from '@/layouts/SystemSettingsLayout.vue';
import { getGeneralSetting } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

const page = usePage();
const { t } = useI18n();
const generalSettings = computed(() => page.props.generalSettings);
const currentWorkspace = computed(() => page.props.currentWorkspace);
const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('基础设置'),
    href: getGeneralSetting.url(currentWorkspace.value.slug),
  },
]);
const logoPreview = ref<string>(generalSettings.value.logo_url || '');
const logoId = ref<string>(generalSettings.value.logo_id || '');
const uploading = ref(false);
const selectedLogoFileName = ref<string>('');

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
    logoPreview.value = generalSettings.value.logo_url || '';
    selectedLogoFileName.value = '';
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
            SystemSetting.UpdateGeneralSettingAction.form(currentWorkspace.slug)
          "
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <Label for="base_url">{{ t('主机地址') }}</Label>
            <Input
              id="base_url"
              name="base_url"
              type="url"
              class="mt-1 block w-full"
              :default-value="generalSettings.base_url || undefined"
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
              :default-value="generalSettings.name || undefined"
              required
              :placeholder="t('请输入系统名称')"
            />
            <InputError class="mt-2" :message="errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="logo_id">{{ t('系统Logo') }}</Label>
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
            <InputError class="mt-2" :message="errors.logo_id" />
          </div>

          <div class="grid gap-2">
            <Label for="copyright">{{ t('版权信息') }}</Label>
            <Input
              id="copyright"
              name="copyright"
              class="mt-1 block w-full"
              :default-value="generalSettings.copyright || undefined"
              :placeholder="t('请输入版权信息')"
            />
            <InputError class="mt-2" :message="errors.copyright" />
          </div>

          <div class="grid gap-2">
            <Label for="icp_record">{{ t('备案信息') }}</Label>
            <Input
              id="icp_record"
              name="icp_record"
              class="mt-1 block w-full"
              :default-value="generalSettings.icp_record || undefined"
              :placeholder="t('请输入备案信息')"
            />
            <InputError class="mt-2" :message="errors.icp_record" />
          </div>

          <div class="grid gap-2">
            <Label>{{ t('版本号') }}</Label>
            <div class="py-2 text-sm text-muted-foreground">
              {{ generalSettings.version || t('未设置') }}
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
