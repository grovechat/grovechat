<script setup lang="ts">
import CheckStorageSettingAction from '@/actions/App/Actions/StorageSetting/CheckStorageSettingAction';
import UpdateStorageSettingAction from '@/actions/App/Actions/StorageSetting/UpdateStorageSettingAction';
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import InputError from '@/components/common/InputError.vue';
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
import { getStorageSetting } from '@/routes';
import type { AppPageProps } from '@/types';
import { type BreadcrumbItem } from '@/types';
import type { StorageProfileData, StorageSettingPagePropsData } from '@/types/generated';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage<AppPageProps<StorageSettingPagePropsData>>();
const { t } = useI18n();
const slug = computed(() => page.props.currentWorkspace.slug);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('存储设置'),
    href: getStorageSetting.url(page.props.currentWorkspace.slug),
  },
]);

const settingsForm = useForm({
  enabled: page.props.storage_settings.enabled,
  current_profile_id: page.props.storage_settings.current_profile_id || '',
});
const actionForm = useForm({});

const saveSettings = () => {
  settingsForm.put(UpdateStorageSettingAction.url(slug.value), {
    preserveScroll: true,
  });
};

const showCreate = ref(false);

const createForm = useForm({
  name: '',
  provider: 'aws',
  region: '',
  endpoint: '',
  key: '',
  secret: '',
  bucket: '',
  url: '',
});

const checkCreateForm = useForm({
  provider: '',
  region: '',
  endpoint: '',
  key: '',
  secret: '',
  bucket: '',
  url: '',
});

const useInternalEndpoint = ref<boolean>(false);

const currentProvider = computed(() =>
  page.props.storage_config.find((p) => p.value === createForm.provider),
);

const currentRegions = computed(() => currentProvider.value?.regions || []);

const currentRegionData = computed(() =>
  currentRegions.value.find((r) => r.id === createForm.region),
);

const isAliyun = computed(() => createForm.provider === 'aliyun');

const hasInternalEndpoint = computed(
  () => isAliyun.value && currentRegionData.value?.internal_endpoint,
);

watch(
  () => createForm.provider,
  (newProvider) => {
    const provider = page.props.storage_config.find(
      (p) => p.value === newProvider,
    );
    if (provider && provider.regions.length > 0) {
      createForm.region = '';
      createForm.endpoint = '';
      useInternalEndpoint.value = false;
    }
  },
);

watch(
  () => createForm.region,
  (newRegion) => {
    const region = currentRegions.value.find((r) => r.id === newRegion);
    if (region) {
      createForm.endpoint = region.endpoint;
      useInternalEndpoint.value = false;
    }
  },
);

const toggleInternalEndpoint = () => {
  if (currentRegionData.value) {
    if (useInternalEndpoint.value) {
      createForm.endpoint = currentRegionData.value.endpoint;
      useInternalEndpoint.value = false;
    } else {
      createForm.endpoint =
        currentRegionData.value.internal_endpoint ||
        currentRegionData.value.endpoint;
      useInternalEndpoint.value = true;
    }
  }
};

const profilesBaseUrl = computed(
  () => `/w/${slug.value}/system-settings/storage/profiles`,
);

const createProfile = () => {
  createForm.post(profilesBaseUrl.value, {
    preserveScroll: true,
    onSuccess: () => {
      showCreate.value = false;
      createForm.reset();
      createForm.secret = '';
    },
  });
};

const checkConnectionForCreate = () => {
  checkCreateForm.clearErrors();
  checkCreateForm.provider = createForm.provider;
  checkCreateForm.region = createForm.region;
  checkCreateForm.endpoint = createForm.endpoint;
  checkCreateForm.key = createForm.key;
  checkCreateForm.secret = createForm.secret;
  checkCreateForm.bucket = createForm.bucket;
  checkCreateForm.url = createForm.url;

  checkCreateForm.put(CheckStorageSettingAction.url(slug.value), {
    preserveScroll: true,
    onSuccess: () => {
      // 不自动清空，方便用户接着创建
    },
  });
};

const editingProfileId = ref<string | null>(null);

const editForm = useForm({
  name: '',
  url: '',
  key: '',
  secret: '',
});

const startEdit = (profile: StorageProfileData) => {
  editingProfileId.value = profile.id;
  editForm.name = profile.name;
  editForm.url = profile.url || '';
  editForm.key = '';
  editForm.secret = '';
};

const cancelEdit = () => {
  editingProfileId.value = null;
  editForm.reset();
  editForm.secret = '';
};

const saveEdit = (profileId: string) => {
  editForm.put(`${profilesBaseUrl.value}/${profileId}`, {
    preserveScroll: true,
    onSuccess: () => {
      cancelEdit();
    },
  });
};

const checkProfile = (profileId: string) => {
  actionForm.put(`${profilesBaseUrl.value}/${profileId}/check`, {
    preserveScroll: true,
  });
};

const deleteProfile = (profileId: string) => {
  // inertia useForm 支持 delete
  actionForm.delete(`${profilesBaseUrl.value}/${profileId}`, {
    preserveScroll: true,
  });
};

const providerLabel = (value: string) =>
  page.props.storage_config.find((p) => p.value === value)?.label || value;
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('存储设置')" />

    <SystemSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('存储设置')"
          :description="
            t('配置对象存储服务，支持 Amazon S3 和阿里云 OSS 等兼容服务')
          "
        />

        <form class="space-y-6" @submit.prevent="saveSettings">
          <div class="grid gap-2">
            <div class="flex items-center space-x-2">
              <Checkbox id="enabled" v-model="settingsForm.enabled" />
              <Label for="enabled" class="cursor-pointer">
                {{ t('启用对象存储') }}
              </Label>
            </div>
            <p class="text-sm text-muted-foreground">
              {{ t('启用后，文件将上传到配置的对象存储服务') }}
            </p>
            <InputError class="mt-2" :message="settingsForm.errors.enabled" />
          </div>

          <div v-show="settingsForm.enabled" class="space-y-6">
            <div class="grid gap-2">
              <Label for="current_profile_id">{{ t('当前使用的存储配置') }}</Label>
              <Select
                v-model="settingsForm.current_profile_id"
                :default-value="settingsForm.current_profile_id"
              >
                <SelectTrigger id="current_profile_id">
                  <SelectValue :placeholder="t('请选择存储配置')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="p in page.props.storage_profiles"
                    :key="p.id"
                    :value="p.id"
                  >
                    {{ p.name }}（{{ providerLabel(p.provider) }}）
                  </SelectItem>
                </SelectContent>
              </Select>
              <InputError
                class="mt-2"
                :message="settingsForm.errors.current_profile_id"
              />
            </div>
          </div>

          <div class="flex items-center gap-4">
            <Button
              type="submit"
              :disabled="settingsForm.processing"
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
              <p
                v-show="settingsForm.recentlySuccessful"
                class="text-sm text-neutral-600"
              >
                {{ t('已保存。') }}
              </p>
            </Transition>
          </div>
        </form>

        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <HeadingSmall
              :title="t('存储配置管理')"
              :description="t('支持创建多个存储配置，并选择其中一个作为当前上传目标')"
            />
            <Button type="button" variant="outline" @click="showCreate = !showCreate">
              {{ showCreate ? t('收起') : t('新增配置') }}
            </Button>
          </div>

          <div v-show="showCreate" class="rounded-lg border p-4 space-y-4">
            <div class="grid gap-2">
              <Label for="name">{{ t('配置名称') }}</Label>
              <Input id="name" v-model="createForm.name" :placeholder="t('例如：腾讯云 COS（生产）')" />
              <InputError class="mt-1" :message="createForm.errors.name" />
            </div>

            <div class="grid gap-2">
              <Label for="provider">{{ t('存储提供商') }}</Label>
              <Select v-model="createForm.provider" :default-value="createForm.provider">
                <SelectTrigger id="provider">
                  <SelectValue :placeholder="t('请选择存储提供商')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="providerOption in page.props.storage_config"
                    :key="providerOption.value"
                    :value="providerOption.value"
                  >
                    {{ providerOption.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p class="text-sm text-muted-foreground">
                <a
                  :href="currentProvider?.help_link"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-primary hover:underline"
                >
                  {{ t('查看文档') }}
                </a>
              </p>
              <InputError class="mt-1" :message="createForm.errors.provider" />
            </div>

            <div class="grid gap-2">
              <Label for="region">{{ t('区域 (Region)') }}</Label>
              <Select v-model="createForm.region" :default-value="createForm.region">
                <SelectTrigger id="region">
                  <template v-if="currentRegionData">
                    <div class="flex items-baseline gap-2">
                      <span class="text-sm">{{ currentRegionData.name }}</span>
                      <span class="font-mono text-xs text-muted-foreground">
                        {{ currentRegionData.id }}
                      </span>
                    </div>
                  </template>
                  <SelectValue v-else :placeholder="t('请选择区域')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="regionOption in currentRegions"
                    :key="regionOption.id"
                    :value="regionOption.id"
                  >
                    <div class="flex items-baseline gap-2">
                      <span class="text-sm">{{ regionOption.name }}</span>
                      <span class="font-mono text-xs text-muted-foreground">
                        {{ regionOption.id }}
                      </span>
                    </div>
                  </SelectItem>
                </SelectContent>
              </Select>
              <InputError class="mt-1" :message="createForm.errors.region || checkCreateForm.errors.region" />
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
                  {{
                    useInternalEndpoint
                      ? t('使用外网 Endpoint')
                      : t('使用内网 Endpoint')
                  }}
                </Button>
              </div>
              <Input id="endpoint" type="url" v-model="createForm.endpoint" />
              <InputError class="mt-1" :message="createForm.errors.endpoint || checkCreateForm.errors.endpoint" />
            </div>

            <div class="grid gap-2">
              <Label for="bucket">{{ t('Bucket 名称') }}</Label>
              <Input id="bucket" v-model="createForm.bucket" :placeholder="t('请输入 Bucket 名称')" />
              <InputError class="mt-1" :message="createForm.errors.bucket || checkCreateForm.errors.bucket" />
            </div>

            <div class="grid gap-2">
              <Label for="key">{{ t('Access Key / Access Key ID') }}</Label>
              <Input id="key" v-model="createForm.key" :placeholder="t('请输入 Access Key')" />
              <InputError class="mt-1" :message="createForm.errors.key || checkCreateForm.errors.key" />
            </div>

            <div class="grid gap-2">
              <Label for="secret">{{ t('Secret Key / Access Key Secret') }}</Label>
              <Input
                id="secret"
                type="password"
                autocomplete="off"
                v-model="createForm.secret"
                :placeholder="t('请输入 Secret')"
              />
              <InputError class="mt-1" :message="createForm.errors.secret || checkCreateForm.errors.secret" />
            </div>

            <div class="grid gap-2">
              <Label for="url">{{ t('自定义域名 (可选)') }}</Label>
              <Input id="url" type="url" v-model="createForm.url" :placeholder="t('例如：https://cdn.example.com')" />
              <InputError class="mt-1" :message="createForm.errors.url || checkCreateForm.errors.url" />
            </div>

            <div class="flex items-center gap-3">
              <Button type="button" variant="outline" :disabled="createForm.processing || checkCreateForm.processing" @click="checkConnectionForCreate">
                {{ t('检测连接') }}
              </Button>
              <Button type="button" :disabled="createForm.processing || checkCreateForm.processing" @click="createProfile">
                {{ t('创建') }}
              </Button>
            </div>
          </div>

          <div class="space-y-3">
            <div
              v-for="p in page.props.storage_profiles"
              :key="p.id"
              class="rounded-lg border p-4 space-y-3"
            >
              <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                  <div class="font-medium">
                    {{ p.name }}
                    <span class="text-muted-foreground">（{{ providerLabel(p.provider) }}）</span>
                  </div>
                  <dl class="mt-2 grid gap-1 text-sm text-muted-foreground">
                    <div class="grid grid-cols-[84px_1fr] items-baseline gap-2">
                      <dt class="font-mono opacity-70">bucket</dt>
                      <dd
                        class="font-mono min-w-0 truncate whitespace-nowrap"
                        :title="p.bucket || '-'"
                      >
                        {{ p.bucket || '-' }}
                      </dd>
                    </div>
                    <div class="grid grid-cols-[84px_1fr] items-baseline gap-2">
                      <dt class="font-mono opacity-70">region</dt>
                      <dd
                        class="font-mono min-w-0 truncate whitespace-nowrap"
                        :title="p.region || '-'"
                      >
                        {{ p.region || '-' }}
                      </dd>
                    </div>
                    <div class="grid grid-cols-[84px_1fr] items-baseline gap-2">
                      <dt class="font-mono opacity-70">endpoint</dt>
                      <dd
                        class="font-mono min-w-0 break-words whitespace-normal"
                        :title="p.endpoint || '-'"
                      >
                        {{ p.endpoint || '-' }}
                      </dd>
                    </div>
                    <div class="grid grid-cols-[84px_1fr] items-baseline gap-2">
                      <dt class="font-mono opacity-70">url</dt>
                      <dd
                        class="font-mono min-w-0 break-words whitespace-normal"
                        :title="p.url || '-'"
                      >
                        {{ p.url || '-' }}
                      </dd>
                    </div>
                    <div class="grid grid-cols-[84px_1fr] items-baseline gap-2">
                      <dt class="font-mono opacity-70">key</dt>
                      <dd
                        class="font-mono min-w-0 truncate whitespace-nowrap"
                        :title="p.key_masked || '-'"
                      >
                        {{ p.key_masked || '-' }}
                      </dd>
                    </div>
                  </dl>
                </div>

                <div class="flex items-center gap-2">
                  <Button type="button" variant="outline" size="sm" @click="checkProfile(p.id)">
                    {{ t('检测连接') }}
                  </Button>
                  <Button type="button" variant="outline" size="sm" @click="startEdit(p)">
                    {{ t('编辑') }}
                  </Button>
                  <Button type="button" variant="destructive" size="sm" @click="deleteProfile(p.id)">
                    {{ t('删除') }}
                  </Button>
                </div>
              </div>

              <div v-if="editingProfileId === p.id" class="rounded-md bg-muted/30 p-3 space-y-3">
                <div class="grid gap-2">
                  <Label>{{ t('配置名称') }}</Label>
                  <Input v-model="editForm.name" />
                  <InputError class="mt-1" :message="editForm.errors.name" />
                </div>
                <div class="grid gap-2">
                  <Label>{{ t('自定义域名 (可选)') }}</Label>
                  <Input v-model="editForm.url" type="url" />
                  <InputError class="mt-1" :message="editForm.errors.url" />
                </div>
                <div class="grid gap-2">
                  <Label>{{ t('Access Key / Access Key ID') }}</Label>
                  <Input v-model="editForm.key" :placeholder="t('留空表示不修改')" />
                  <InputError class="mt-1" :message="editForm.errors.key" />
                </div>
                <div class="grid gap-2">
                  <Label>{{ t('Secret Key / Access Key Secret') }}</Label>
                  <Input v-model="editForm.secret" type="password" autocomplete="off" :placeholder="t('留空表示不修改')" />
                  <InputError class="mt-1" :message="editForm.errors.secret" />
                </div>
                <div class="flex items-center gap-2">
                  <Button type="button" :disabled="editForm.processing" @click="saveEdit(p.id)">
                    {{ t('保存') }}
                  </Button>
                  <Button type="button" variant="outline" :disabled="editForm.processing" @click="cancelEdit">
                    {{ t('取消') }}
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </SystemSettingsLayout>
  </AppLayout>
</template>
