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
  deleteWorkspace,
  getWorkspaceList,
  getWorkspaceTrash,
  loginAsWorkspaceOwner,
  showWorkspaceDetail,
} from '@/routes';
import type { AppPageProps, BreadcrumbItem } from '@/types';
import type { WorkspaceListPagePropsData } from '@/types/generated';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { formatDateTime } = useDateTime();
const page = usePage<AppPageProps<WorkspaceListPagePropsData>>();
const workspaceList = computed(() => page.props.workspace_list);
const deleteForm = useForm({});
const currentUserId = computed(() =>
  String((page.props as any)?.auth?.user?.id ?? ''),
);

const cannotDelete = (ws: (typeof workspaceList.value)[number]) =>
  !!ws.owner?.id && String(ws.owner.id) === currentUserId.value;
const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('工作区管理'),
    href: getWorkspaceList.url(),
  },
]);
</script>
<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('工作区管理')" />
    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
        <div class="space-y-6">
          <div class="flex items-start justify-between gap-4">
            <HeadingSmall
              :title="t('工作区管理')"
              :description="t('查看系统中所有工作区及其成员信息')"
            />
            <Button variant="outline" as-child>
              <Link :href="getWorkspaceTrash.url()">{{ t('回收站') }}</Link>
            </Button>
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
                      {{ t('成员数') }}
                    </th>
                    <th class="px-4 py-3 text-right font-medium">
                      {{ t('操作') }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="ws in workspaceList"
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
                      <div class="font-medium">
                        {{ ws.owner?.name || '-' }}
                      </div>
                      <div class="text-xs text-muted-foreground">
                        {{ ws.owner?.email || '' }}
                      </div>
                    </td>
                    <td class="px-4 py-3 text-muted-foreground">
                      {{ formatDateTime(ws.created_at) }}
                    </td>
                    <td class="px-4 py-3">
                      {{ ws.members_count }}
                    </td>
                    <td class="px-4 py-3 text-right">
                      <div class="inline-flex items-center gap-2">
                        <Button variant="outline" size="sm" as-child>
                          <Link :href="showWorkspaceDetail.url(ws.id)">
                            {{ t('查看详情') }}
                          </Link>
                        </Button>

                        <Button
                          v-if="ws.owner?.id"
                          variant="outline"
                          size="sm"
                          as-child
                        >
                          <a
                            :href="loginAsWorkspaceOwner.url(ws.id)"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            {{ t('以所有者打开') }}
                          </a>
                        </Button>

                        <Dialog>
                          <DialogTrigger as-child>
                            <Button
                              variant="destructive"
                              size="sm"
                              :disabled="cannotDelete(ws)"
                              :title="
                                cannotDelete(ws)
                                  ? t('不能删除自己作为所有者的工作区')
                                  : undefined
                              "
                            >
                              {{ t('删除') }}
                            </Button>
                          </DialogTrigger>
                          <DialogContent>
                            <DialogHeader class="space-y-3">
                              <DialogTitle>
                                {{ t('确认删除工作区？') }}
                              </DialogTitle>
                              <DialogDescription>
                                {{ t('将工作区放入回收站，可以后续恢复。') }}
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
                                  :disabled="deleteForm.processing"
                                >
                                  {{ t('取消') }}
                                </Button>
                              </DialogClose>
                              <Button
                                variant="destructive"
                                :disabled="deleteForm.processing"
                                @click="
                                  deleteForm.delete(
                                    deleteWorkspace.url(ws.id),
                                    {
                                      preserveScroll: true,
                                    },
                                  )
                                "
                              >
                                {{
                                  deleteForm.processing
                                    ? t('删除中...')
                                    : t('确认删除')
                                }}
                              </Button>
                            </DialogFooter>
                          </DialogContent>
                        </Dialog>
                      </div>
                    </td>
                  </tr>

                  <tr v-if="workspaceList.length === 0">
                    <td
                      colspan="5"
                      class="px-4 py-8 text-center text-muted-foreground"
                    >
                      {{ t('暂无工作区') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SystemAppLayout>
</template>
