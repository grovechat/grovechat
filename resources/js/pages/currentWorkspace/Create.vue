<script setup lang="ts">
import UploadImageAction from '@/actions/App/Actions/Attachment/UploadImageAction';
import CreateWorkspaceAction from '@/actions/App/Actions/Manage/CreateWorkspaceAction';
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import ImageUploadField from '@/components/common/ImageUploadField.vue';
import InputError from '@/components/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useI18n } from '@/composables/useI18n';
import { useRequiredWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import { type AppPageProps, type BreadcrumbItem } from '@/types';
import { Form, Head, usePage } from '@inertiajs/vue3';
import { Check, Copy } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const { t } = useI18n();
const page = usePage<AppPageProps>();
const generalSettings = computed(() => page.props.generalSettings);
const currentWorkspace = useRequiredWorkspace();
const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('创建工作区'),
    href: '#',
  },
]);
const slugInput = ref<string>('');
const copied = ref(false);

// 计算完整的访问路径
const fullAccessUrl = computed(() => {
  const baseUrl = generalSettings.value?.base_url || '';
  return `${baseUrl}/w/${slugInput.value}`;
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
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('创建工作区')" />

    <WorkspaceSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('创建工作区')"
          :description="t('创建一个新的工作区来组织你的团队和项目')"
        />

        <Form
          v-bind="CreateWorkspaceAction.form(currentWorkspace.slug)"
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

          <ImageUploadField
            :label="t('Logo')"
            name="logo_id"
            :upload-url="UploadImageAction.url()"
            response-key="id"
            :initial-preview="''"
            :initial-value="''"
            variant="logo"
            :error="errors.logo"
          />

          <div class="grid gap-2">
            <Label for="slug">{{ t('访问路径') }}</Label>
            <Input
              id="slug"
              name="slug"
              class="mt-1 block w-full"
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
              data-test="create-workspace-button"
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
    </WorkspaceSettingsLayout>
  </AppLayout>
</template>
