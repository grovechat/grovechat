<script setup lang="ts">
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { useI18n } from '@/composables/useI18n';
import { useRequiredWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import {
  removeTeammate,
  showCreateTeammatePage,
  showEditTeammatePage,
  showTeammateList,
  updateTeammateOnlineStatus,
} from '@/routes';
import { useDateTime } from '@/composables/useDateTime';

import type { BreadcrumbItem } from '@/types';
import type {
  ShowListTeammatePagePropsData,
} from '@/types/generated';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { t } = useI18n();
const props = defineProps<ShowListTeammatePagePropsData>();
const currentWorkspace = useRequiredWorkspace();
const updatingStatusIds = ref<Record<string, boolean>>({});
const removeForm = useForm({});
const { formatDateTime } = useDateTime();
const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('多客服'),
    href: showTeammateList.url(currentWorkspace.value.slug),
  },
]);

const handleOnlineStatusChange = (userId: string, status: number) => {
  updatingStatusIds.value[userId] = true;
  router.put(
    updateTeammateOnlineStatus.url({
      slug: currentWorkspace.value.slug,
      id: userId,
    }),
    { online_status: Number(status) },
    {
      preserveScroll: true,
      preserveState: true,
      onFinish: () => {
        updatingStatusIds.value[userId] = false;
      },
    },
  );
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('多客服')" />

    <WorkspaceSettingsLayout content-class="max-w-none">
      <div class="space-y-6">
        <div class="flex items-start justify-between gap-4">
          <HeadingSmall :title="t('多客服')" :description="t('管理客服账号')" />

          <div class="inline-flex items-center gap-2">
            <Button as-child>
              <Link :href="showCreateTeammatePage.url(currentWorkspace.slug)">
                {{ t('新增客服') }}
              </Link>
            </Button>
          </div>
        </div>

        <div class="rounded-lg border">
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="border-b bg-muted/30 text-muted-foreground">
                <tr class="text-left">
                  <th class="px-4 py-3">{{ t('头像') }}</th>
                  <th class="px-4 py-3">{{ t('名称') }}</th>
                  <th class="px-4 py-3">{{ t('对外昵称') }}</th>
                  <th class="px-4 py-3">{{ t('邮箱') }}</th>
                  <th class="px-4 py-3">{{ t('身份') }}</th>
                  <th class="px-4 py-3">{{ t('在线状态') }}</th>
                  <th class="px-4 py-3">{{ t('最后活跃时间') }}</th>
                  <th class="px-4 py-3 text-right">{{ t('操作') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="u in props.user_list"
                  :key="u.user_id"
                  class="border-t bg-background"
                >
                  <td class="px-4 py-3">
                    <Avatar class="h-9 w-9">
                      <AvatarImage v-if="u.user_avatar" :src="u.user_avatar" />
                      <AvatarFallback>
                        {{ (u.user_name || '').slice(0, 1) }}
                      </AvatarFallback>
                    </Avatar>
                  </td>
                  <td class="px-4 py-3 font-medium">
                    {{ u.user_name }}
                  </td>
                  <td class="px-4 py-3">
                    {{ u.user_nickname || '-' }}
                  </td>
                  <td class="px-4 py-3">
                    {{ u.user_email }}
                  </td>
                  <td class="px-4 py-3">
                    {{ u.role.label }}
                  </td>
                  <td class="px-4 py-3">
                    <Select
                      :model-value="String(u.user_online_status.value)"
                      :disabled="updatingStatusIds[u.user_id]"
                      @update:model-value="
                        (v) => handleOnlineStatusChange(u.user_id, v)
                      "
                    >
                      <SelectTrigger class="h-9 w-28">
                        <SelectValue
                          :placeholder="u.user_online_status.label"
                        >
                          {{ u.user_online_status.label }}
                        </SelectValue>
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem
                          v-for="opt in props.online_status_options"
                          :key="String(opt.value)"
                          :value="String(opt.value)"
                        >
                          {{ opt.label }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  </td>
                  <td class="px-4 py-3">
                    {{ u.user_last_active_at ? formatDateTime(u.user_last_active_at) : '-' }}
                  </td>
                  <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                      <Button as-child variant="outline" size="sm">
                        <Link
                          :href="
                            showEditTeammatePage.url({
                              slug: currentWorkspace.slug,
                              id: u.user_id,
                            })
                          "
                        >
                          {{ t('编辑') }}
                        </Link>
                      </Button>
                      <Dialog>
                        <DialogTrigger as-child>
                          <Button
                            variant="destructive"
                            size="sm"
                            :disabled="!u.show_remove_button || removeForm.processing"
                            :title="
                              !u.show_remove_button
                                ? t('当前登录用户不允许删除')
                                : undefined
                            "
                          >
                            {{ t('移除') }}
                          </Button>
                        </DialogTrigger>
                        <DialogContent>
                          <DialogHeader class="space-y-3">
                            <DialogTitle>
                              {{ t('确认移除客服？') }}
                            </DialogTitle>
                            <DialogDescription>
                              {{ t('将从当前工作区移除该成员的访问权限（不会删除用户）。') }}
                            </DialogDescription>
                          </DialogHeader>

                          <div class="rounded-md bg-muted/30 p-3 text-sm">
                            <div class="font-medium">{{ u.user_name }}</div>
                            <div class="text-muted-foreground">
                              {{ u.user_email }}
                            </div>
                          </div>

                          <DialogFooter class="gap-2">
                            <DialogClose as-child>
                              <Button
                                variant="secondary"
                                :disabled="removeForm.processing"
                              >
                                {{ t('取消') }}
                              </Button>
                            </DialogClose>
                            <Button
                              variant="destructive"
                              :disabled="
                                removeForm.processing || !u.show_remove_button
                              "
                              @click="
                                removeForm.delete(
                                  removeTeammate.url({
                                    slug: currentWorkspace.slug,
                                    id: u.user_id,
                                  }),
                                  { preserveScroll: true },
                                )
                              "
                            >
                              {{
                                removeForm.processing
                                  ? t('移除中...')
                                  : t('确认移除')
                              }}
                            </Button>
                          </DialogFooter>
                        </DialogContent>
                      </Dialog>
                    </div>
                  </td>
                </tr>

                <tr v-if="props.user_list.length === 0">
                  <td
                    class="px-4 py-8 text-center text-muted-foreground"
                    colspan="7"
                  >
                    {{ t('暂无客服') }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </WorkspaceSettingsLayout>
  </AppLayout>
</template>
