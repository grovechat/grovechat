<script setup lang="ts">
import CommonController from '@/actions/App/Http/Controllers/Api/CommonController';
import TenantSettingController from '@/actions/App/Http/Controllers/TenantSettingController';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import { useTenant } from '@/composables/useTenant';
import AppLayout from '@/layouts/AppLayout.vue';
import TenantSettingsLayout from '@/layouts/TenantSettingsLayout.vue';
import tenantSetting from '@/routes/tenant-setting';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Check, Copy } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Tenant {
  id: number;
  name: string;
  slug: string;
  logo: string | null;
  path: string;
  owner_id: number | null;
}

const props = defineProps<{
  tenant: Tenant;
}>();

const { t } = useI18n();
const { tenantPath } = useTenant();
const page = usePage();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('常规设置'),
    href: tenantPath.value
      ? tenantSetting.tenant.general.url(tenantPath.value)
      : '#',
  },
]);

const logoPreview = ref<string>(props.tenant.logo || '');
const logoUrl = ref<string>(props.tenant.logo || '');
const uploading = ref(false);
const pathInput = ref<string>(props.tenant.path || '');
const copied = ref(false);
const showDeleteDialog = ref(false);
const deleting = ref(false);

// 从共享数据中获取 generalSettings
const generalSettings = computed(() => page.props.generalSettings as any);

// 计算完整的访问路径
const fullAccessUrl = computed(() => {
  const baseUrl = generalSettings.value?.baseUrl || '';
  return `${baseUrl}/w/${pathInput.value}`;
});

// 判断是否是默认工作区
const isDefaultTenant = computed(() => {
  return props.tenant.owner_id !== null;
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
    logoPreview.value = props.tenant.logo || '';
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

const handleDelete = () => {
  if (!tenantPath.value) return;

  deleting.value = true;
  router.delete(
    TenantSettingController.deleteTenant.url(tenantPath.value),
    {
      preserveState: false,
      preserveScroll: false,
      onSuccess: () => {
        showDeleteDialog.value = false;
      },
      onFinish: () => {
        deleting.value = false;
      },
    },
  );
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('常规设置')" />

    <TenantSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('常规设置')"
          :description="t('配置工作区的基本信息和设置')"
        />

        <Form
          v-bind="
            tenantPath
              ? TenantSettingController.updateTenent.form(tenantPath)
              : {}
          "
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <Label for="slug">{{ t('工作区ID') }}</Label>
            <Input
              id="slug"
              name="slug"
              class="mt-1 block w-full bg-gray-50"
              :default-value="tenant.slug"
              disabled
              readonly
            />
            <p class="text-sm text-muted-foreground">
              {{ t('工作区ID不可修改') }}
            </p>
          </div>

          <div class="grid gap-2">
            <Label for="name">{{ t('工作区名称') }}</Label>
            <Input
              id="name"
              name="name"
              class="mt-1 block w-full"
              :default-value="tenant.name"
              required
              :placeholder="t('请输入工作区名称')"
            />
            <InputError class="mt-2" :message="errors.name" />
          </div>

          <div class="grid gap-2">
            <Label for="logo">{{ t('工作区Logo') }}</Label>
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
              :default-value="tenant.path"
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
              data-test="update-tenant-button"
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

        <div class="border-t pt-8 mt-12">
          <HeadingSmall
            :title="t('危险操作')"
            :description="t('删除工作区将无法恢复，请谨慎操作')"
          />
          <div class="mt-6">
            <Button
              variant="destructive"
              :disabled="isDefaultTenant"
              @click="showDeleteDialog = true"
            >
              {{ t('删除工作区') }}
            </Button>
            <p v-if="isDefaultTenant" class="text-sm text-muted-foreground mt-2">
              {{ t('默认工作区不能删除') }}
            </p>
          </div>
        </div>
      </div>
    </TenantSettingsLayout>

    <Dialog v-model:open="showDeleteDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ t('确认删除工作区') }}</DialogTitle>
          <DialogDescription>
            {{ t('删除工作区后，所有相关数据将被永久删除，此操作无法撤销。确定要继续吗？') }}
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button
            variant="outline"
            @click="showDeleteDialog = false"
            :disabled="deleting"
          >
            {{ t('取消') }}
          </Button>
          <Button
            variant="destructive"
            @click="handleDelete"
            :disabled="deleting"
          >
            {{ deleting ? t('删除中...') : t('确认删除') }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
