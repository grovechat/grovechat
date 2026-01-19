<script setup lang="ts">
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import InputError from '@/components/common/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useDateTime } from '@/composables/useDateTime';
import { useI18n } from '@/composables/useI18n';
import { useRequiredWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import { createTag, deleteTag, updateTag } from '@/routes';
import workspaceSetting from '@/routes/workspace-setting';
import type { BreadcrumbItem } from '@/types';
import type {
  FormCreateTagData,
  FormUpdateTagData,
  ListTagItemData,
  ShowListTagPagePropsData,
} from '@/types/generated';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { t } = useI18n();
const { formatDateTime } = useDateTime();
const props = defineProps<ShowListTagPagePropsData>();
const currentWorkspace = useRequiredWorkspace();

const DEFAULT_COLOR = '#64748b';

const createOpen = ref(false);
const editOpen = ref(false);
const editingTag = ref<ListTagItemData | null>(null);

const createForm = useForm<FormCreateTagData>({
  name: '',
  color: DEFAULT_COLOR,
  description: null,
});

const editForm = useForm<FormUpdateTagData>({
  name: '',
  color: DEFAULT_COLOR,
  description: null,
});

const deleteForm = useForm({});

const createColor = computed<string>({
  get: () => createForm.color ?? DEFAULT_COLOR,
  set: (v) => {
    createForm.color = v === '' ? null : v;
  },
});

const createDescription = computed<string>({
  get: () => createForm.description ?? '',
  set: (v) => {
    createForm.description = v === '' ? null : v;
  },
});

const editColor = computed<string>({
  get: () => editForm.color ?? DEFAULT_COLOR,
  set: (v) => {
    editForm.color = v === '' ? null : v;
  },
});

const editDescription = computed<string>({
  get: () => editForm.description ?? '',
  set: (v) => {
    editForm.description = v === '' ? null : v;
  },
});

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('标签'),
    href: workspaceSetting.datas.tag.url(currentWorkspace.value.slug),
  },
]);

const openEdit = (tag: ListTagItemData) => {
  editingTag.value = tag;
  editForm.name = tag.name;
  editForm.color = tag.color ?? DEFAULT_COLOR;
  editForm.description = tag.description ?? null;
  editForm.clearErrors();
  editOpen.value = true;
};

const submitCreate = () => {
  createForm.post(createTag.url(currentWorkspace.value.slug), {
    preserveScroll: true,
    onSuccess: () => {
      createForm.reset();
      createOpen.value = false;
    },
  });
};

const submitEdit = () => {
  if (!editingTag.value) {
    return;
  }

  editForm.put(
    updateTag.url({
      slug: currentWorkspace.value.slug,
      id: editingTag.value.id,
    }),
    {
      preserveScroll: true,
      onSuccess: () => {
        editOpen.value = false;
        editingTag.value = null;
      },
    },
  );
};

const submitDelete = (tagId: string) => {
  deleteForm.delete(
    deleteTag.url({
      slug: currentWorkspace.value.slug,
      id: tagId,
    }),
    { preserveScroll: true },
  );
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('标签')" />

    <WorkspaceSettingsLayout content-class="max-w-none">
      <div class="space-y-6">
        <div class="flex items-start justify-between gap-4">
          <HeadingSmall :title="t('标签')" :description="t('管理和组织联系人标签')" />

          <Dialog v-model:open="createOpen">
            <DialogTrigger as-child>
              <Button>
                {{ t('新增标签') }}
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader class="space-y-3">
                <DialogTitle>{{ t('新增标签') }}</DialogTitle>
              </DialogHeader>

              <div class="space-y-4">
                <div class="space-y-2">
                  <Label for="create-name">{{ t('名称') }}</Label>
                  <Input
                    id="create-name"
                    v-model="createForm.name"
                    :placeholder="t('例如：VIP')"
                    :disabled="createForm.processing"
                  />
                  <InputError :message="createForm.errors.name" />
                </div>

                <div class="space-y-2">
                  <Label for="create-color">{{ t('颜色') }}</Label>
                  <div class="flex items-center gap-2">
                    <input
                      id="create-color"
                      v-model="createColor"
                      class="h-9 w-12 cursor-pointer rounded-md border bg-background p-1"
                      type="color"
                      :disabled="createForm.processing"
                    />
                    <Input
                      v-model="createColor"
                      class="h-9"
                      :disabled="createForm.processing"
                    />
                  </div>
                  <InputError :message="createForm.errors.color" />
                </div>

                <div class="space-y-2">
                  <Label for="create-desc">{{ t('描述') }}</Label>
                  <Input
                    id="create-desc"
                    v-model="createDescription"
                    :placeholder="t('可选')"
                    :disabled="createForm.processing"
                  />
                  <InputError :message="createForm.errors.description" />
                </div>
              </div>

              <DialogFooter class="gap-2">
                <DialogClose as-child>
                  <Button variant="secondary" :disabled="createForm.processing">
                    {{ t('取消') }}
                  </Button>
                </DialogClose>
                <Button :disabled="createForm.processing" @click="submitCreate">
                  {{ createForm.processing ? t('保存中...') : t('保存') }}
                </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>
        </div>

        <div class="rounded-lg border">
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="border-b bg-muted/30 text-muted-foreground">
                <tr class="text-left">
                  <th class="px-4 py-3">{{ t('名称') }}</th>
                  <th class="px-4 py-3">{{ t('颜色') }}</th>
                  <th class="px-4 py-3">{{ t('描述') }}</th>
                  <th class="px-4 py-3">{{ t('更新时间') }}</th>
                  <th class="px-4 py-3 text-right">{{ t('操作') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="tag in props.tag_list"
                  :key="tag.id"
                  class="border-t bg-background"
                >
                  <td class="px-4 py-3 font-medium">
                    {{ tag.name }}
                  </td>
                  <td class="px-4 py-3">
                    <Badge
                      class="border"
                      :style="{
                        backgroundColor: tag.color ?? undefined,
                        borderColor: tag.color ?? undefined,
                        color: tag.color ? 'white' : undefined,
                      }"
                    >
                      {{ tag.color ?? '-' }}
                    </Badge>
                  </td>
                  <td class="px-4 py-3 text-muted-foreground">
                    {{ tag.description || '-' }}
                  </td>
                  <td class="px-4 py-3">
                    {{ tag.updated_at ? formatDateTime(tag.updated_at) : '-' }}
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                      <Button
                        variant="outline"
                        size="sm"
                        :disabled="editForm.processing || deleteForm.processing"
                        @click="openEdit(tag)"
                      >
                        {{ t('编辑') }}
                      </Button>

                      <Dialog>
                        <DialogTrigger as-child>
                          <Button
                            variant="destructive"
                            size="sm"
                            :disabled="deleteForm.processing"
                          >
                            {{ t('删除') }}
                          </Button>
                        </DialogTrigger>
                        <DialogContent>
                          <DialogHeader class="space-y-3">
                            <DialogTitle>{{ t('确认删除标签？') }}</DialogTitle>
                          </DialogHeader>

                          <div class="rounded-md bg-muted/30 p-3 text-sm">
                            <div class="font-medium">{{ tag.name }}</div>
                            <div class="text-muted-foreground">
                              {{ tag.description || '-' }}
                            </div>
                          </div>

                          <DialogFooter class="gap-2">
                            <DialogClose as-child>
                              <Button
                                variant="secondary"
                                :disabled="deleteForm.processing"
                              >
                                {{ t('取消') }}
                              </Button>
                            </DialogClose>
                            <Button
                              variant="destructive"
                              :disabled="deleteForm.processing"
                              @click="submitDelete(tag.id)"
                            >
                              {{
                                deleteForm.processing ? t('删除中...') : t('确认删除')
                              }}
                            </Button>
                          </DialogFooter>
                        </DialogContent>
                      </Dialog>
                    </div>
                  </td>
                </tr>

                <tr v-if="props.tag_list.length === 0">
                  <td
                    class="px-4 py-8 text-center text-muted-foreground"
                    colspan="5"
                  >
                    {{ t('暂无标签') }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <Dialog v-model:open="editOpen">
          <DialogContent>
            <DialogHeader class="space-y-3">
              <DialogTitle>{{ t('编辑标签') }}</DialogTitle>
            </DialogHeader>

            <div class="space-y-4">
              <div class="space-y-2">
                <Label for="edit-name">{{ t('名称') }}</Label>
                <Input
                  id="edit-name"
                  v-model="editForm.name"
                  :disabled="editForm.processing"
                />
                <InputError :message="editForm.errors.name" />
              </div>

              <div class="space-y-2">
                <Label for="edit-color">{{ t('颜色') }}</Label>
                <div class="flex items-center gap-2">
                  <input
                    id="edit-color"
                    v-model="editColor"
                    class="h-9 w-12 cursor-pointer rounded-md border bg-background p-1"
                    type="color"
                    :disabled="editForm.processing"
                  />
                  <Input v-model="editColor" :disabled="editForm.processing" />
                </div>
                <InputError :message="editForm.errors.color" />
              </div>

              <div class="space-y-2">
                <Label for="edit-desc">{{ t('描述') }}</Label>
                <Input
                  id="edit-desc"
                  v-model="editDescription"
                  :disabled="editForm.processing"
                />
                <InputError :message="editForm.errors.description" />
              </div>
            </div>

            <DialogFooter class="gap-2">
              <DialogClose as-child>
                <Button variant="secondary" :disabled="editForm.processing">
                  {{ t('取消') }}
                </Button>
              </DialogClose>
              <Button :disabled="editForm.processing" @click="submitEdit">
                {{ editForm.processing ? t('保存中...') : t('保存') }}
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>
    </WorkspaceSettingsLayout>
  </AppLayout>
</template>

