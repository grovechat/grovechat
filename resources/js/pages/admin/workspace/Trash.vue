<script setup lang="ts">
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { useDateTime } from '@/composables/useDateTime';
import { useI18n } from '@/composables/useI18n';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import {
  getWorkspaceList,
  getWorkspaceTrash,
  restoreWorkspace,
} from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import type { ShowWorkspaceTrashPagePropsData } from '@/types/generated';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { formatDateTime } = useDateTime();
const props = defineProps<ShowWorkspaceTrashPagePropsData>();
const restoreForm = useForm({});

const prevPage = computed(() =>
  Math.max(1, props.workspace_trash_list_pagination.current_page - 1),
);
const nextPage = computed(() =>
  Math.min(
    props.workspace_trash_list_pagination.last_page,
    props.workspace_trash_list_pagination.current_page + 1,
  ),
);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('工作区管理'),
    href: getWorkspaceList.url(),
  },
  {
    title: t('工作区回收站'),
    href: getWorkspaceTrash.url(),
  },
]);
</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('工作区回收站')" />
    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
        <div class="space-y-6">
          <div class="flex items-start justify-between gap-4">
            <HeadingSmall
              :title="t('工作区回收站')"
              :description="t('查看已删除的工作区并可恢复')"
            />
          </div>

          <div class="rounded-lg border">
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="border-b bg-muted/30 text-muted-foreground">
                  <tr>
                    <th class="px-4 py-3 text-left font-medium">
                      {{ t('名称') }}
                    </th>
                    <th class="px-4 py-3 text-left font-medium">
                      {{ t('所有者') }}
                    </th>
                    <th class="px-4 py-3 text-left font-medium">
                      {{ t('创建时间') }}
                    </th>
                    <th class="px-4 py-3 text-left font-medium">
                      {{ t('删除时间') }}
                    </th>
                    <th class="px-4 py-3 text-left font-medium">
                      {{ t('成员数') }}
                    </th>
                    <th class="px-4 py-3 text-right font-medium">
                      {{ t('操作') }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="ws in props.workspace_trash_list"
                    :key="ws.id"
                    class="border-b last:border-b-0"
                  >
                    <td class="px-4 py-3">
                      <div class="font-medium">{{ ws.name }}</div>
                      <div class="text-xs text-muted-foreground">
                        {{ ws.slug || '-' }}
                      </div>
                    </td>
                    <td class="px-4 py-3">
                      <div class="font-medium">{{ ws.owner?.name || '-' }}</div>
                      <div class="text-xs text-muted-foreground">
                        {{ ws.owner?.email || '' }}
                      </div>
                    </td>
                    <td class="px-4 py-3 text-muted-foreground">
                      {{ formatDateTime(ws.created_at) }}
                    </td>
                    <td class="px-4 py-3 text-muted-foreground">
                      {{ ws.deleted_at ? formatDateTime(ws.deleted_at) : '-' }}
                    </td>
                    <td class="px-4 py-3">
                      {{ ws.members_count }}
                    </td>
                    <td class="px-4 py-3 text-right">
                      <Dialog>
                        <DialogTrigger as-child>
                          <Button
                            variant="outline"
                            size="sm"
                            :disabled="restoreForm.processing"
                          >
                            {{ t('恢复') }}
                          </Button>
                        </DialogTrigger>
                        <DialogContent>
                          <DialogHeader class="space-y-3">
                            <DialogTitle>
                              {{ t('确认恢复工作区？') }}
                            </DialogTitle>
                            <DialogDescription>
                              {{ t('恢复后将重新出现在工作区管理列表中。') }}
                            </DialogDescription>
                          </DialogHeader>

                          <div class="rounded-md bg-muted/30 p-3 text-sm">
                            <div class="font-medium">{{ ws.name }}</div>
                            <div class="text-muted-foreground">
                              {{ ws.owner?.name || '-' }}
                            </div>
                          </div>

                          <DialogFooter class="gap-2">
                            <DialogClose as-child>
                              <Button
                                variant="secondary"
                                :disabled="restoreForm.processing"
                              >
                                {{ t('取消') }}
                              </Button>
                            </DialogClose>
                            <Button
                              variant="outline"
                              :disabled="restoreForm.processing"
                              @click="
                                restoreForm.put(restoreWorkspace.url(ws.id), {
                                  preserveScroll: true,
                                })
                              "
                            >
                              {{
                                restoreForm.processing
                                  ? t('恢复中...')
                                  : t('确认恢复')
                              }}
                            </Button>
                          </DialogFooter>
                        </DialogContent>
                      </Dialog>
                    </td>
                  </tr>

                  <tr v-if="props.workspace_trash_list.length === 0">
                    <td
                      colspan="6"
                      class="px-4 py-8 text-center text-muted-foreground"
                    >
                      {{ t('暂无已删除的工作区') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex items-center justify-between gap-3 border-t p-4">
              <div class="text-sm text-muted-foreground">
                {{ t('第') }} {{ props.workspace_trash_list_pagination.current_page }}
                / {{ props.workspace_trash_list_pagination.last_page }}
                {{ t('页，共') }}
                {{ props.workspace_trash_list_pagination.total }}
                {{ t('个工作区') }}
              </div>
              <div class="flex items-center gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  :disabled="props.workspace_trash_list_pagination.current_page <= 1"
                  as-child
                >
                  <Link :href="getWorkspaceTrash.url({ query: { page: prevPage } })">
                    {{ t('上一页') }}
                  </Link>
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  :disabled="
                    props.workspace_trash_list_pagination.current_page >=
                    props.workspace_trash_list_pagination.last_page
                  "
                  as-child
                >
                  <Link :href="getWorkspaceTrash.url({ query: { page: nextPage } })">
                    {{ t('下一页') }}
                  </Link>
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SystemAppLayout>
</template>
