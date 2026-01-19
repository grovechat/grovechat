<script setup lang="ts">
import UploadImageAction from '@/actions/App/Actions/Attachment/UploadImageAction';
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import ImageUploadField from '@/components/common/ImageUploadField.vue';
import InputError from '@/components/common/InputError.vue';
import { Button } from '@/components/ui/button';
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
import admin from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import type { ShowEditWorkspacePagePropsData } from '@/types/generated';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { t } = useI18n();
const props = defineProps<ShowEditWorkspacePagePropsData>();
const page = usePage<any>();
const generalSettings = computed(() => page.props.generalSettings);

const ownerId = ref<string>(props.workspace.owner_id || '');
const slugInput = ref<string>(props.workspace.slug || '');

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  { title: t('工作区管理'), href: admin.getWorkspaceList.url() },
  {
    title: props.workspace.name || t('编辑'),
    href: admin.showEditWorkspacePage.url(props.workspace.id),
  },
]);

const fullAccessUrl = computed(() => {
  const baseUrl = generalSettings.value?.base_url || '';
  return `${baseUrl}/w/${slugInput.value}`;
});
</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('编辑工作区')" />

    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
        <div class="space-y-6">
          <HeadingSmall
            :title="t('编辑工作区')"
            :description="t('修改工作区基础信息并可调整所有者')"
          />

          <Form
            v-bind="admin.updateWorkspace.form(props.workspace.id)"
            class="space-y-6"
            v-slot="{ errors, processing, recentlySuccessful }"
          >
            <div class="grid gap-2">
              <Label for="name">{{ t('名称') }}</Label>
              <Input
                id="name"
                name="name"
                class="mt-1 block w-full"
                required
                :default-value="props.workspace.name"
                :placeholder="t('请输入工作区名称')"
              />
              <InputError class="mt-2" :message="errors.name" />
            </div>

            <ImageUploadField
              :label="t('工作区Logo')"
              name="logo_id"
              :upload-url="UploadImageAction.url()"
              response-key="id"
              :initial-preview="props.workspace.logo_url || ''"
              :initial-value="props.workspace.logo_id || ''"
              variant="logo"
              :error="errors.logo_id"
            />

            <div class="grid gap-2">
              <Label for="slug">{{ t('访问路径') }}</Label>
              <Input
                id="slug"
                name="slug"
                class="mt-1 block w-full"
                v-model="slugInput"
                required
                :default-value="props.workspace.slug || ''"
                :placeholder="t('请输入访问路径')"
              />
              <p class="text-sm text-muted-foreground">{{ fullAccessUrl }}</p>
              <InputError class="mt-2" :message="errors.slug" />
            </div>

            <div class="grid gap-2">
              <Label for="owner_id">{{ t('所有者') }}</Label>
              <input type="hidden" name="owner_id" :value="ownerId" />
              <Select v-model="ownerId">
                <SelectTrigger id="owner_id" class="mt-1">
                  <SelectValue :placeholder="t('请选择所有者')" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="u in props.owner_options"
                    :key="String(u.id)"
                    :value="String(u.id)"
                  >
                    {{ u.name }} ({{ u.email }})
                  </SelectItem>
                </SelectContent>
              </Select>
              <InputError class="mt-2" :message="errors.owner_id" />
            </div>

            <div class="flex items-center gap-4">
              <Button type="submit" :disabled="processing">
                {{ t('保存') }}
              </Button>

              <Button variant="outline" as-child :disabled="processing">
                <Link :href="admin.showWorkspaceDetail.url(props.workspace.id)">
                  {{ t('返回') }}
                </Link>
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
      </div>
    </div>
  </SystemAppLayout>
</template>
