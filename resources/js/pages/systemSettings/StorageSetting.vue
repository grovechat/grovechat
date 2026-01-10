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
import type { StorageSettingData, StorageConfigData } from '@/types/generated';

const page = usePage();
const { t } = useI18n();
const storageSettings = computed(() => page.props.storageSettings as StorageSettingData);
const storageConfig = computed(() => page.props.storageConfig as StorageConfigData[]);
const currentWorkspace = computed(() => page.props.currentWorkspace);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('存储设置'),
    href: systemSetting.getStorageSettings.url(currentWorkspace.value.slug),
  },
]);

const enabled = ref<boolean>(storageSettings.value.enabled);
const provider = ref<string>(storageSettings.value.provider || 'aws');
const selectedRegion = ref<string>(storageSettings.value.region || '');
const endpoint = ref<string>(storageSettings.value.endpoint || '');
const key = ref<string>(storageSettings.value.key || '');
const secret = ref<string>(storageSettings.value.secret || '');
const bucket = ref<string>(storageSettings.value.bucket || '');
const url = ref<string>(storageSettings.value.url || '');
const useInternalEndpoint = ref<boolean>(false);

const currentProvider = computed(() =>
  storageConfig.value.find(p => p.value === provider.value)
);

const currentRegions = computed(() =>
  currentProvider.value?.regions || []
);

const currentRegionData = computed(() =>
  currentRegions.value.find(r => r.id === selectedRegion.value)
);

const isAliyun = computed(() => provider.value === 'aliyun');

const hasInternalEndpoint = computed(() =>
  isAliyun.value && currentRegionData.value?.internal_endpoint
);

watch(provider, (newProvider) => {
  const provider = storageConfig.value.find(p => p.value === newProvider);
  if (provider && provider.regions.length > 0) {
    selectedRegion.value = '';
    endpoint.value = '';
    useInternalEndpoint.value = false;
  }
});

// Watch region changes
watch(selectedRegion, (newRegion) => {
  const region = currentRegions.value.find(r => r.id === newRegion);
  if (region) {
    endpoint.value = region.endpoint;
    useInternalEndpoint.value = false;
  }
});

const toggleInternalEndpoint = () => {
  if (currentRegionData.value) {
    if (useInternalEndpoint.value) {
      endpoint.value = currentRegionData.value.endpoint;
      useInternalEndpoint.value = false;
    } else {
      endpoint.value = currentRegionData.value.internal_endpoint || currentRegionData.value.endpoint;
      useInternalEndpoint.value = true;
    }
  }
};
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

          <input type="hidden" name="enabled" :value="enabled" />

          <div v-show="enabled" class="space-y-6">
            <div class="grid gap-2">
              <Label for="provider">{{ t('存储提供商') }}</Label>
              <Select
                v-model="provider"
                name="provider"
                :default-value="provider"
              >
                <SelectTrigger id="provider">
                  <SelectValue :placeholder="t('请选择存储提供商')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="provider in storageConfig"
                    :key="provider.value"
                    :value="provider.value"
                  >
                    {{ provider.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p class="text-sm text-muted-foreground">
                <a :href="currentProvider?.help_link"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-primary hover:underline"
                >
                  {{ t('查看文档') }}
                </a>
              </p>
              <InputError class="mt-2" :message="errors.disk" />
            </div>

            <div class="grid gap-2">
              <Label for="region">{{ t('区域 (Region)') }}</Label>
              <Select
                v-model="selectedRegion"
                name="region"
                :default-value="selectedRegion"
              >
                <SelectTrigger id="region">
                  <SelectValue :placeholder="t('请选择区域')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="region in currentRegions"
                    :key="region.id"
                    :value="region.id"
                  >
                    {{ region.id }} ({{ region.name }})
                  </SelectItem>
                </SelectContent>
              </Select>
              <InputError class="mt-2" :message="errors.region" />
            </div>

            <div class="grid gap-2">
              <div class="flex items-center justify-between">
                <Label for="endpoint">{{ t('Endpoint 地址') }}</Label>
                <Button
                  v-if="hasInternalEndpoint"
                  type="button"
                  variant="outline"
                  size="sm"
                  @click="toggleInternalEndpoint"
                >
                  {{ useInternalEndpoint ? t('使用外网 Endpoint') : t('使用内网 Endpoint') }}
                </Button>
              </div>
              <Input
                id="endpoint"
                name="endpoint"
                type="url"
                class="mt-1 block w-full"
                v-model="endpoint"
                :placeholder="t('例如：https://s3.amazonaws.com')"
              />
              <p v-if="hasInternalEndpoint && useInternalEndpoint" class="text-sm text-amber-600">
                {{ t('如果服务器和对象存储在同一区域，建议使用内网 Endpoint 以提高速度并节省流量费用') }}
              </p>
              <InputError class="mt-2" :message="errors.endpoint" />
            </div>

            <div class="grid gap-2">
              <Label for="key">{{ t('Access Key / Access Key ID') }}</Label>
              <Input
                id="key"
                name="key"
                type="text"
                class="mt-1 block w-full"
                v-model="key"
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
                v-model="secret"
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
                v-model="bucket"
                required
                :placeholder="t('请输入 Bucket 名称')"
              />
              <InputError class="mt-2" :message="errors.bucket" />
            </div>

            <div class="grid gap-2">
              <Label for="url">{{ t('自定义域名 (可选)') }}</Label>
              <Input
                id="url"
                name="url"
                type="url"
                class="mt-1 block w-full"
                v-model="url"
                :placeholder="t('例如：https://cdn.example.com')"
              />
              <p class="text-sm text-muted-foreground">
                {{ t('如果配置了 CDN 或自定义域名，请在此填写，用于生成文件访问 URL') }}
              </p>
              <InputError class="mt-2" :message="errors.url" />
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


