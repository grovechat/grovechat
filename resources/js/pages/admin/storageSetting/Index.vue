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
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import { getStorageSetting } from '@/routes/admin';
import storageProfile from '@/routes/admin/storage-profile';
import { type BreadcrumbItem } from '@/types';
import type {
  StorageProfileData,
  GetStorageSettingPagePropsData,
  FormCreateStorageProfileData,
  FormCheckStorageSettingData,
  FormUpdateStorageProfileData,
  FormStorageSettingData,
} from '@/types/generated';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps<GetStorageSettingPagePropsData>();
const { t } = useI18n();

const nullToEmpty = (value: string | null | undefined): string => value ?? '';
const emptyToNull = (value: string): string | null => (value === '' ? null : value);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('存储设置'),
    href: getStorageSetting.url(),
  },
]);

const settingsForm = useForm<FormStorageSettingData>({
  enabled: props.settings.enabled,
  current_profile_id: props.settings.current_profile_id,
});
const actionForm = useForm({});

const settingsCurrentProfileId = computed<string>({
  get: () => nullToEmpty(settingsForm.current_profile_id),
  set: (value) => {
    settingsForm.current_profile_id = emptyToNull(value);
  },
});

const saveSettings = () => {
  settingsForm.put(UpdateStorageSettingAction.url(), {
    preserveScroll: true,
  });
};

const showCreate = ref(false);

const createForm = useForm<FormCreateStorageProfileData>({
  name: '',
  provider: 'aws',
  region: '',
  endpoint: '',
  key: '',
  secret: '',
  bucket: '',
  url: null,
});

const createUrl = computed<string>({
  get: () => nullToEmpty(createForm.url),
  set: (value) => {
    createForm.url = emptyToNull(value);
  },
});

const checkCreateForm = useForm<FormCheckStorageSettingData>({
  provider: '',
  region: '',
  endpoint: '',
  key: '',
  secret: null,
  bucket: '',
  url: null,
});

const useInternalEndpoint = ref<boolean>(false);

const currentProvider = computed(() =>
  props.providers.find((p) => p.provider.value === createForm.provider),
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
    const provider = props.providers.find((p) => p.provider.value === newProvider);
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

const createProfile = () => {
  createForm.post(storageProfile.create.url(), {
    preserveScroll: true,
    onSuccess: () => {
      showCreate.value = false;
      createForm.reset();
      createForm.secret = '';
    },
  });
};

const getCheckCreateData = (): FormCheckStorageSettingData => ({
  provider: createForm.provider,
  region: createForm.region,
  endpoint: createForm.endpoint,
  key: createForm.key,
  secret: createForm.secret,
  bucket: createForm.bucket,
  url: createForm.url,
});

const errorForCreate = (field: string): string | undefined =>
  (createForm.errors as Record<string, string | undefined>)[field] ??
  (checkCreateForm.errors as Record<string, string | undefined>)[field];

const checkConnectionForCreate = () => {
  checkCreateForm.clearErrors();
  Object.assign(checkCreateForm, getCheckCreateData());

  checkCreateForm.put(CheckStorageSettingAction.url(), {
    preserveScroll: true,
    onSuccess: () => {
      // 不自动清空，方便用户接着创建
    },
  });
};

const editingProfileId = ref<string | null>(null);

const editForm = useForm<FormUpdateStorageProfileData>({
  name: '',
  url: null,
  key: null,
  secret: null,
});

const editUrl = computed<string>({
  get: () => nullToEmpty(editForm.url),
  set: (value) => {
    editForm.url = emptyToNull(value);
  },
});

const editKey = computed<string>({
  get: () => nullToEmpty(editForm.key),
  set: (value) => {
    editForm.key = emptyToNull(value);
  },
});

const editSecret = computed<string>({
  get: () => nullToEmpty(editForm.secret),
  set: (value) => {
    editForm.secret = emptyToNull(value);
  },
});

const startEdit = (profile: StorageProfileData) => {
  editingProfileId.value = profile.id;
  editForm.name = profile.name;
  editForm.url = profile.url ?? null;
  editForm.key = null;
  editForm.secret = null;
};

const cancelEdit = () => {
  editingProfileId.value = null;
  editForm.reset();
  editForm.secret = null;
};

const saveEdit = (profileId: string) => {
  editForm.put(storageProfile.update.url(profileId), {
    preserveScroll: true,
    onSuccess: () => {
      cancelEdit();
    },
  });
};

const checkProfile = (profileId: string) => {
  actionForm.put(storageProfile.check.url(profileId), {
    preserveScroll: true,
  });
};

const deleteProfile = (profileId: string) => {
  // inertia useForm 支持 delete
  actionForm.delete(storageProfile.delete.url(profileId), {
    preserveScroll: true,
  });
};

</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('存储设置')" />
    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
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
                <Label for="current_profile_id">{{
                  t('当前使用的存储配置')
                }}</Label>
                <Select
                  v-model="settingsCurrentProfileId"
                  :default-value="settingsCurrentProfileId"
                >
                  <SelectTrigger id="current_profile_id">
                    <SelectValue :placeholder="t('请选择存储配置')" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem
                      v-for="p in props.profiles"
                      :key="p.id"
                      :value="p.id"
                    >
                      {{ p.name }}（{{ p.provider.label }}）
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
                :description="
                  t('支持创建多个存储配置，并选择其中一个作为当前上传目标')
                "
              />
              <Button
                type="button"
                variant="outline"
                @click="showCreate = !showCreate"
              >
                {{ showCreate ? t('收起') : t('新增配置') }}
              </Button>
            </div>

            <form
              v-show="showCreate"
              class="space-y-4 rounded-lg border p-4"
              @submit.prevent="createProfile"
            >
              <div class="grid gap-2">
                <Label for="name">{{ t('配置名称') }}</Label>
                <Input
                  id="name"
                  v-model="createForm.name"
                  :placeholder="t('例如：腾讯云 COS（生产）')"
                />
                <InputError class="mt-1" :message="createForm.errors.name" />
              </div>

              <div class="grid gap-2">
                <Label for="provider">{{ t('存储提供商') }}</Label>
                <Select
                  v-model="createForm.provider"
                  :default-value="createForm.provider"
                >
                  <SelectTrigger id="provider">
                    <SelectValue :placeholder="t('请选择存储提供商')" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem
                      v-for="providerOption in props.providers"
                      :key="providerOption.provider.value"
                      :value="providerOption.provider.value"
                    >
                      {{ providerOption.provider.label }}
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
                <InputError
                  class="mt-1"
                  :message="createForm.errors.provider"
                />
              </div>

              <div class="grid gap-2">
                <Label for="region">{{ t('区域 (Region)') }}</Label>
                <Select
                  v-model="createForm.region"
                  :default-value="createForm.region"
                >
                  <SelectTrigger id="region">
                    <template v-if="currentRegionData">
                      <div class="flex items-baseline gap-2">
                        <span class="text-sm">{{
                          currentRegionData.name
                        }}</span>
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
                <InputError
                  class="mt-1"
                  :message="
                    errorForCreate('region')
                  "
                />
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
                <InputError
                  class="mt-1"
                  :message="
                    errorForCreate('endpoint')
                  "
                />
              </div>

              <div class="grid gap-2">
                <Label for="bucket">{{ t('Bucket 名称') }}</Label>
                <Input
                  id="bucket"
                  v-model="createForm.bucket"
                  :placeholder="t('请输入 Bucket 名称')"
                />
                <InputError
                  class="mt-1"
                  :message="
                    errorForCreate('bucket')
                  "
                />
              </div>

              <div class="grid gap-2">
                <Label for="key">{{ t('Access Key / Access Key ID') }}</Label>
                <Input
                  id="key"
                  v-model="createForm.key"
                  :placeholder="t('请输入 Access Key')"
                />
                <InputError
                  class="mt-1"
                  :message="errorForCreate('key')"
                />
              </div>

              <div class="grid gap-2">
                <Label for="secret">{{
                  t('Secret Key / Access Key Secret')
                }}</Label>
                <Input
                  id="secret"
                  type="password"
                  autocomplete="off"
                  v-model="createForm.secret"
                  :placeholder="t('请输入 Secret Key')"
                />
                <InputError
                  class="mt-1"
                  :message="
                    errorForCreate('secret')
                  "
                />
              </div>

              <div class="grid gap-2">
                <Label for="url">{{ t('自定义域名 (可选)') }}</Label>
                <Input
                  id="url"
                  type="url"
                  v-model="createUrl"
                  :placeholder="t('例如：https://cdn.example.com')"
                />
                <InputError
                  class="mt-1"
                  :message="errorForCreate('url')"
                />
              </div>

              <div class="flex items-center gap-3">
                <Button
                  type="button"
                  variant="outline"
                  :disabled="
                    createForm.processing || checkCreateForm.processing
                  "
                  @click="checkConnectionForCreate"
                >
                  {{ t('检测连接') }}
                </Button>
                <Button
                  type="submit"
                  :disabled="
                    createForm.processing || checkCreateForm.processing
                  "
                >
                  {{ t('创建') }}
                </Button>
              </div>
            </form>

            <div class="space-y-3">
              <div
                v-for="p in props.profiles"
                :key="p.id"
                class="space-y-3 rounded-lg border p-4"
              >
                <div class="flex items-start justify-between gap-4">
                  <div class="space-y-1">
                    <div class="font-medium">
                      {{ p.name }}
                      <span class="text-muted-foreground"
                        >（{{ p.provider.label }}）</span
                      >
                    </div>
                    <dl class="mt-2 grid gap-1 text-sm text-muted-foreground">
                      <div
                        class="grid grid-cols-[84px_1fr] items-baseline gap-2"
                      >
                        <dt class="font-mono opacity-70">bucket</dt>
                        <dd
                          class="min-w-0 truncate font-mono whitespace-nowrap"
                          :title="p.bucket || '-'"
                        >
                          {{ p.bucket || '-' }}
                        </dd>
                      </div>
                      <div
                        class="grid grid-cols-[84px_1fr] items-baseline gap-2"
                      >
                        <dt class="font-mono opacity-70">region</dt>
                        <dd
                          class="min-w-0 truncate font-mono whitespace-nowrap"
                          :title="p.region || '-'"
                        >
                          {{ p.region || '-' }}
                        </dd>
                      </div>
                      <div
                        class="grid grid-cols-[84px_1fr] items-baseline gap-2"
                      >
                        <dt class="font-mono opacity-70">endpoint</dt>
                        <dd
                          class="min-w-0 font-mono wrap-break-word whitespace-normal"
                          :title="p.endpoint || '-'"
                        >
                          {{ p.endpoint || '-' }}
                        </dd>
                      </div>
                      <div
                        class="grid grid-cols-[84px_1fr] items-baseline gap-2"
                      >
                        <dt class="font-mono opacity-70">url</dt>
                        <dd
                          class="min-w-0 font-mono wrap-break-word whitespace-normal"
                          :title="p.url || '-'"
                        >
                          {{ p.url || '-' }}
                        </dd>
                      </div>
                      <div
                        class="grid grid-cols-[84px_1fr] items-baseline gap-2"
                      >
                        <dt class="font-mono opacity-70">key</dt>
                        <dd
                          class="min-w-0 truncate font-mono whitespace-nowrap"
                          :title="p.key_masked || '-'"
                        >
                          {{ p.key_masked || '-' }}
                        </dd>
                      </div>
                    </dl>
                  </div>

                  <div class="flex items-center gap-2">
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="checkProfile(p.id)"
                    >
                      {{ t('检测连接') }}
                    </Button>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      @click="startEdit(p)"
                    >
                      {{ t('编辑') }}
                    </Button>
                    <Button
                      type="button"
                      variant="destructive"
                      size="sm"
                      @click="deleteProfile(p.id)"
                    >
                      {{ t('删除') }}
                    </Button>
                  </div>
                </div>

                <form
                  v-if="editingProfileId === p.id"
                  class="space-y-3 rounded-md bg-muted/30 p-3"
                  @submit.prevent="saveEdit(p.id)"
                >
                  <div class="grid gap-2">
                    <Label>{{ t('配置名称') }}</Label>
                    <Input v-model="editForm.name" />
                    <InputError class="mt-1" :message="editForm.errors.name" />
                  </div>
                  <div class="grid gap-2">
                    <Label>{{ t('自定义域名 (可选)') }}</Label>
                    <Input v-model="editUrl" type="url" />
                    <InputError class="mt-1" :message="editForm.errors.url" />
                  </div>
                  <div class="grid gap-2">
                    <Label>{{ t('Access Key / Access Key ID') }}</Label>
                    <Input
                      v-model="editKey"
                      :placeholder="t('留空表示不修改')"
                    />
                    <InputError class="mt-1" :message="editForm.errors.key" />
                  </div>
                  <div class="grid gap-2">
                    <Label>{{ t('Secret Key / Access Key Secret') }}</Label>
                    <Input
                      v-model="editSecret"
                      type="password"
                      autocomplete="off"
                      :placeholder="t('留空表示不修改')"
                    />
                    <InputError
                      class="mt-1"
                      :message="editForm.errors.secret"
                    />
                  </div>
                  <div class="flex items-center gap-2">
                    <Button type="submit" :disabled="editForm.processing">
                      {{ t('保存') }}
                    </Button>
                    <Button
                      type="button"
                      variant="outline"
                      :disabled="editForm.processing"
                      @click="cancelEdit"
                    >
                      {{ t('取消') }}
                    </Button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SystemAppLayout>
</template>
