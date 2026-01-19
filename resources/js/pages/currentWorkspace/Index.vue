<script setup lang="ts">
import UploadImageAction from '@/actions/App/Actions/Attachment/UploadImageAction';
import DeleteCurrentWorkspaceAction from '@/actions/App/Actions/Manage/DeleteCurrentWorkspaceAction';
import UpdateWorkspaceAction from '@/actions/App/Actions/Manage/UpdateWorkspaceAction';
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import ImageUploadField from '@/components/common/ImageUploadField.vue';
import InputError from '@/components/common/InputError.vue';
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
import { useRequiredWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import { getCurrentWorkspace } from '@/routes';
import { type AppPageProps, type BreadcrumbItem } from '@/types';
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import { Check, Copy } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const { t } = useI18n();
const page = usePage<AppPageProps>();
const generalSettings = computed(() => page.props.generalSettings);
const currentWorkspace = useRequiredWorkspace();
const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('常规设置'),
    href: getCurrentWorkspace.url(currentWorkspace.value.slug),
  },
]);

const slugInput = ref<string>(currentWorkspace.value.slug || '');
const copied = ref(false);
const showDeleteDialog = ref(false);
const deleting = ref(false);

// 计算完整的访问路径
const fullAccessUrl = computed(() => {
  const baseUrl = generalSettings.value?.base_url || '';
  return `${baseUrl}/w/${slugInput.value}`;
});

// 判断是否是默认工作区
const isDefaultWorkspace = computed(() => {
  return currentWorkspace.value.owner_id !== null;
});

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
  deleting.value = true;
  router.delete(DeleteCurrentWorkspaceAction.url(currentWorkspace.value.slug), {
    preserveState: false,
    preserveScroll: false,
    onSuccess: () => {
      showDeleteDialog.value = false;
    },
    onFinish: () => {
      deleting.value = false;
    },
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('常规设置')" />

    <WorkspaceSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('常规设置')"
          :description="t('配置工作区的基本信息和设置')"
        />

        <Form
          v-bind="UpdateWorkspaceAction.form(currentWorkspace.slug)"
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <Label for="slug">{{ t('工作区ID') }}</Label>
            <Input
              id="slug"
              name="slug"
              class="mt-1 block w-full bg-gray-50"
              :default-value="currentWorkspace.slug"
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
              :default-value="currentWorkspace.name"
              required
              :placeholder="t('请输入工作区名称')"
            />
            <InputError class="mt-2" :message="errors.name" />
          </div>

          <ImageUploadField
            :label="t('工作区Logo')"
            name="logo_id"
            :upload-url="UploadImageAction.url()"
            response-key="id"
            :initial-preview="currentWorkspace.logo_url || ''"
            :initial-value="currentWorkspace.logo_id || ''"
            variant="logo"
            :error="errors.logo"
          />

          <div class="grid gap-2">
            <Label for="slug">{{ t('访问路径') }}</Label>
            <Input
              id="slug"
              name="slug"
              class="mt-1 block w-full"
              :default-value="currentWorkspace.slug"
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
              data-test="update-workspace-button"
            >
              {{ t('保存') }}
            </Button>

            <Button
              type="button"
              variant="destructive"
              :disabled="isDefaultWorkspace"
              @click="showDeleteDialog = true"
            >
              {{ t('删除工作区') }}
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

          <p v-if="isDefaultWorkspace" class="text-sm text-muted-foreground">
            {{ t('默认工作区不能删除') }}
          </p>
        </Form>
      </div>
    </WorkspaceSettingsLayout>

    <Dialog v-model:open="showDeleteDialog">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{{ t('确认删除工作区') }}</DialogTitle>
          <DialogDescription>
            {{
              t(
                '确定要删除该工作区吗？删除后会进入回收站，需要超级管理员才能恢复。',
              )
            }}
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
