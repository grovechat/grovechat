<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { useI18n } from '@/composables/useI18n';
import { useTimezone } from '@/composables/useTimezone';
import AppLayout from '@/layouts/AppLayout.vue';
import SystemSettingsLayout from '@/layouts/SystemSettingsLayout.vue';
import { getWorkspaceList, showWorkspaceDetail } from '@/routes';
import type { AppPageProps, BreadcrumbItem } from '@/types';
import type { WorkspaceListPagePropsData } from '@/types/generated';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { formatToTimezone } = useTimezone();
const page = usePage<AppPageProps<WorkspaceListPagePropsData>>();
const currentWorkspace = computed(() => page.props.currentWorkspace);
const workspaceList = computed(() => page.props.workspace_list);
const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('工作区管理'),
    href: getWorkspaceList.url(currentWorkspace.value.slug),
  },
]);
  
</script> 
<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('工作区管理')" />

    <SystemSettingsLayout content-class="max-w-5xl w-full">
      <div class="space-y-6">
        <HeadingSmall
          :title="t('工作区管理')"
          :description="t('查看系统中所有工作区及其成员信息')"
        />

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
                    {{ formatToTimezone(ws.created_at) }}
                  </td>
                  <td class="px-4 py-3">
                    {{ ws.members_count }}
                  </td>
                  <td class="px-4 py-3 text-right">
                    <Button variant="outline" size="sm" as-child>
                      <Link
                        :href="
                          showWorkspaceDetail.url({
                            slug: currentWorkspace.slug,
                            id: ws.id,
                          })
                        "
                      >
                        {{ t('查看详情') }}
                      </Link>
                    </Button>
                  </td>
                </tr>

                <tr v-if="workspaceList.length === 0">
                  <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">
                    {{ t('暂无工作区') }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </SystemSettingsLayout>
  </AppLayout>
</template>
  