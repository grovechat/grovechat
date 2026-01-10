<script setup lang="ts">
import StorageSetting from '@/actions/App/Actions/StorageSetting';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import AppLayout from '@/layouts/AppLayout.vue';
import SystemSettingsLayout from '@/layouts/SystemSettingsLayout.vue';
import systemSetting from '@/routes/system-setting';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import type { StorageSettingData } from '@/types/generated';

const page = usePage();
const { t } = useI18n();
const storageSettings = computed(() => page.props.storageSettings as StorageSettingData);
const currentWorkspace = computed(() => page.props.currentWorkspace);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('存储设置'),
    href: systemSetting.getStorageSettings.url(currentWorkspace.value.slug),
  },
]);

const enabled = ref<boolean>(storageSettings.value.enabled);
const selectedDisk = ref<string>(storageSettings.value.disk || 's3');
const pathStyle = ref<boolean>(storageSettings.value.path_style);

watch(enabled, (newValue) => {
  if (!newValue) {
    // When disabled, reset to default values
    selectedDisk.value = 's3';
  }
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('存储设置')" />

    <SystemSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('存储设置')"
          :description="t('配置对象存储服务，支持 Amazon S3 和阿里云 OSS 等兼容服务')"
        />

        <Form
          v-bind="StorageSetting.UpdateStorageSettingAction.form(currentWorkspace.slug)"
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <div class="flex items-center space-x-2">
              <Checkbox
                id="enabled"
                name="enabled"
                v-model="enabled"
              />
              <Label for="enabled" class="cursor-pointer">
                {{ t('启用对象存储') }}
              </Label>
            </div>
            <p class="text-sm text-muted-foreground">
              {{ t('启用后，文件将上传到配置的对象存储服务') }}
            </p>
            <InputError class="mt-2" :message="errors.enabled" />
          </div>

          <div v-if="enabled" class="space-y-6">
            <div class="grid gap-2">
              <Label for="disk">{{ t('存储类型') }}</Label>
              <Select
                v-model="selectedDisk"
                name="disk"
                :default-value="selectedDisk"
              >
                <SelectTrigger id="disk">
                  <SelectValue :placeholder="t('请选择存储类型')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="s3">Amazon S3</SelectItem>
                  <SelectItem value="oss">{{ t('阿里云 OSS') }}</SelectItem>
                </SelectContent>
              </Select>
              <InputError class="mt-2" :message="errors.disk" />
            </div>

            <div class="grid gap-2">
              <Label for="key">{{ t('Access Key / Access Key ID') }}</Label>
              <Input
                id="key"
                name="key"
                type="text"
                class="mt-1 block w-full"
                :default-value="storageSettings.key || undefined"
                required
                :placeholder="t('请输入 Access Key')"
              />
              <InputError class="mt-2" :message="errors.key" />
            </div>

            <div class="grid gap-2">
              <Label for="secret">{{ t('Secret Key / Access Key Secret') }}</Label>
              <Input
                id="secret"
                name="secret"
                type="password"
                autocomplete="off"
                class="mt-1 block w-full"
                :default-value="storageSettings.secret || undefined"
                required
                :placeholder="t('请输入 Secret Key')"
              />
              <InputError class="mt-2" :message="errors.secret" />
            </div>

            <div class="grid gap-2">
              <Label for="bucket">{{ t('Bucket 名称') }}</Label>
              <Input
                id="bucket"
                name="bucket"
                type="text"
                class="mt-1 block w-full"
                :default-value="storageSettings.bucket || undefined"
                required
                :placeholder="t('请输入 Bucket 名称')"
              />
              <InputError class="mt-2" :message="errors.bucket" />
            </div>

            <div class="grid gap-2">
              <Label for="region">{{ t('区域 (Region)') }}</Label>
              <Input
                id="region"
                name="region"
                type="text"
                class="mt-1 block w-full"
                :default-value="storageSettings.region || undefined"
                required
                :placeholder="
                  selectedDisk === 'oss'
                    ? t('例如：oss-cn-hangzhou')
                    : t('例如：us-east-1')
                "
              />
              <p class="text-sm text-muted-foreground">
                {{
                  selectedDisk === 'oss'
                    ? t('阿里云 OSS 区域，如：oss-cn-hangzhou, oss-cn-beijing')
                    : t('AWS 区域，如：us-east-1, ap-northeast-1')
                }}
              </p>
              <InputError class="mt-2" :message="errors.region" />
            </div>

            <div class="grid gap-2">
              <Label for="endpoint">{{ t('Endpoint 地址') }}</Label>
              <Input
                id="endpoint"
                name="endpoint"
                type="url"
                class="mt-1 block w-full"
                :default-value="storageSettings.endpoint || undefined"
                required
                :placeholder="
                  selectedDisk === 'oss'
                    ? t('例如：https://oss-cn-hangzhou.aliyuncs.com')
                    : t('例如：https://s3.amazonaws.com')
                "
              />
              <p class="text-sm text-muted-foreground">
                {{
                  selectedDisk === 'oss'
                    ? t('阿里云 OSS Endpoint，通常为 https://oss-{region}.aliyuncs.com')
                    : t('S3 Endpoint，留空使用默认值或填写自定义 S3 兼容服务地址')
                }}
              </p>
              <InputError class="mt-2" :message="errors.endpoint" />
            </div>

            <div class="grid gap-2">
              <Label for="url">{{ t('自定义域名 (可选)') }}</Label>
              <Input
                id="url"
                name="url"
                type="url"
                class="mt-1 block w-full"
                :default-value="storageSettings.url || undefined"
                :placeholder="t('例如：https://cdn.example.com')"
              />
              <p class="text-sm text-muted-foreground">
                {{ t('如果配置了 CDN 或自定义域名，请在此填写，用于生成文件访问 URL') }}
              </p>
              <InputError class="mt-2" :message="errors.url" />
            </div>

            <div class="grid gap-2">
              <div class="flex items-center space-x-2">
                <Checkbox
                  id="path_style"
                  name="path_style"
                  v-model="pathStyle"
                />
                <Label for="path_style" class="cursor-pointer">
                  {{ t('使用 Path Style 访问') }}
                </Label>
              </div>
              <p class="text-sm text-muted-foreground">
                {{
                  t(
                    '启用后使用路径风格访问（path-style），如：https://s3.amazonaws.com/bucket/key，而非虚拟主机风格（virtual-hosted-style）',
                  )
                }}
              </p>
              <InputError class="mt-2" :message="errors.path_style" />
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Button
              type="submit"
              :disabled="processing"
              data-test="update-storage-settings-button"
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

