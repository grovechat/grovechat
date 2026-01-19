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
  showCreateWorkspacePage,
  showEditWorkspacePage,
  showWorkspaceDetail,
} from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import type {
  ShowWorkspaceListPagePropsData,
} from '@/types/generated';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { formatDateTime } = useDateTime();
const deleteForm = useForm({});
const props = defineProps<ShowWorkspaceListPagePropsData>();

const prevPage = computed(() =>
  Math.max(1, props.workspace_list_pagination.current_page - 1),
);
const nextPage = computed(() =>
  Math.min(
    props.workspace_list_pagination.last_page,
    props.workspace_list_pagination.current_page + 1,
  ),
);

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
            <div class="flex items-center gap-2">
              <Button as-child>
                <Link :href="showCreateWorkspacePage.url()">
                  {{ t('新建工作区') }}
                </Link>
              </Button>
              <Button variant="outline" as-child>
                <Link :href="getWorkspaceTrash.url()">{{ t('回收站') }}</Link>
              </Button>
            </div>
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
                    v-for="ws in props.workspace_list"
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
                          <Link :href="showEditWorkspacePage.url(ws.id)">
                            {{ t('编辑') }}
                          </Link>
                        </Button>
                        
                        <Button variant="outline" size="sm" as-child>
                          <Link :href="showWorkspaceDetail.url(ws.id)">
                            {{ t('客服列表') }}
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
                            {{ t('进入工作区') }}
                          </a>
                        </Button>

                        <Dialog>
                          <DialogTrigger as-child>
                            <Button
                              variant="destructive"
                              size="sm"
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

                  <tr v-if="props.workspace_list.length === 0">
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

            <div class="flex items-center justify-between gap-3 border-t p-4">
              <div class="text-sm text-muted-foreground">
                {{ t('第') }} {{ props.workspace_list_pagination.current_page }}
                / {{ props.workspace_list_pagination.last_page }}
                {{ t('页，共') }}
                {{ props.workspace_list_pagination.total }} {{ t('条') }}
              </div>
              <div class="flex items-center gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  :disabled="props.workspace_list_pagination.current_page <= 1"
                  as-child
                >
                  <Link :href="getWorkspaceList.url({ query: { page: prevPage } })">
                    {{ t('上一页') }}
                  </Link>
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  :disabled="
                    props.workspace_list_pagination.current_page >=
                    props.workspace_list_pagination.last_page
                  "
                  as-child
                >
                  <Link :href="getWorkspaceList.url({ query: { page: nextPage } })">
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
