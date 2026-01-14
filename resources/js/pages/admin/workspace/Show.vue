<script setup lang="ts">
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import { useDateTime } from '@/composables/useDateTime';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import { getWorkspaceList, showWorkspaceDetail } from '@/routes';
import type { AppPageProps, BreadcrumbItem } from '@/types';
import type { WorkspaceDetailPagePropsData } from '@/types/generated';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { formatDateTime } = useDateTime();
const page = usePage<AppPageProps<WorkspaceDetailPagePropsData>>();
const workspaceDetail = computed(() => page.props.workspace_detail);
const members = computed(() => page.props.workspace_members);
const pagination = computed(() => page.props.workspace_members_pagination);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('工作区管理'),
    href: getWorkspaceList.url(),
  },
  {
    title: workspaceDetail.value?.name || t('详情'),
    href: showWorkspaceDetail.url(workspaceDetail.value?.id || ''),
  },
]);

const prevPage = computed(() => Math.max(1, pagination.value.current_page - 1));
const nextPage = computed(() =>
  Math.min(pagination.value.last_page, pagination.value.current_page + 1),
);
</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('工作区详情')" />
    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
        <div class="space-y-6">
          <HeadingSmall
            :title="workspaceDetail.name"
            :description="t('查看该工作区的成员列表')"
          />

        <div class="rounded-lg border p-4 space-y-2">
          <div class="grid gap-1 text-sm">
            <div class="flex items-baseline justify-between gap-3">
              <div class="text-muted-foreground">{{ t('所有者') }}</div>
              <div class="font-medium">
                {{ workspaceDetail.owner?.name || '-' }}
                <span class="text-muted-foreground">
                  {{ workspaceDetail.owner?.email ? `(${workspaceDetail.owner.email})` : '' }}
                </span>
              </div>
            </div>
            <div class="flex items-baseline justify-between gap-3">
              <div class="text-muted-foreground">{{ t('创建时间') }}</div>
              <div class="font-medium">
                {{ formatDateTime(workspaceDetail.created_at) }}
              </div>
            </div>
            <div class="flex items-baseline justify-between gap-3">
              <div class="text-muted-foreground">{{ t('成员数') }}</div>
              <div class="font-medium">{{ workspaceDetail.members_count }}</div>
            </div>
          </div>
        </div>

        <div class="rounded-lg border">
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="border-b bg-muted/30 text-muted-foreground">
                <tr>
                  <th class="px-4 py-3 text-left font-medium">
                    {{ t('成员') }}
                  </th>
                  <th class="px-4 py-3 text-left font-medium">
                    {{ t('邮箱') }}
                  </th>
                  <th class="px-4 py-3 text-left font-medium">
                    {{ t('角色') }}
                  </th>
                  <th class="px-4 py-3 text-left font-medium">
                    {{ t('加入时间') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="m in members"
                  :key="m.id"
                  class="border-b last:border-b-0"
                >
                  <td class="px-4 py-3 font-medium">
                    {{ m.name }}
                    <span v-if="m.deleted_at" class="ml-2 text-xs text-muted-foreground">
                      {{ t('已删除') }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-muted-foreground">{{ m.email }}</td>
                  <td class="px-4 py-3">{{ m.role || '-' }}</td>
                  <td class="px-4 py-3 text-muted-foreground">
                    {{ m.joined_at ? formatDateTime(m.joined_at) : '-' }}
                  </td>
                </tr>

                <tr v-if="members.length === 0">
                  <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">
                    {{ t('暂无成员') }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex items-center justify-between gap-3 border-t p-4">
            <div class="text-sm text-muted-foreground">
              {{ t('第') }} {{ pagination.current_page }} / {{ pagination.last_page }}
              {{ t('页，共') }} {{ pagination.total }} {{ t('人') }}
            </div>
            <div class="flex items-center gap-2">
              <Button
                variant="outline"
                size="sm"
                :disabled="pagination.current_page <= 1"
                as-child
              >
                <Link
                  :href="
                    showWorkspaceDetail.url(workspaceDetail.id, {
                      query: { page: prevPage },
                    })
                  "
                >
                  {{ t('上一页') }}
                </Link>
              </Button>
              <Button
                variant="outline"
                size="sm"
                :disabled="pagination.current_page >= pagination.last_page"
                as-child
              >
                <Link
                  :href="
                    showWorkspaceDetail.url(workspaceDetail.id, {
                      query: { page: nextPage },
                    })
                  "
                >
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
