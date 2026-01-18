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
  deleteTeammate,
  showCreateTeammatePage,
  showEditTeammatePage,
  showTeammateList,
  showTeammateTrashPage,
  updateTeammateOnlineStatus,
} from '@/routes';
import type { AppPageProps, BreadcrumbItem } from '@/types';
import type {
  ListTeammatePagePropsData,
} from '@/types/generated';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { t } = useI18n();
const page = usePage<AppPageProps<ListTeammatePagePropsData>>();
const currentWorkspace = useRequiredWorkspace();
const userList = computed(() => page.props.user_list);
const onlineStatusOptions = computed(() => page.props.online_status_options);
const canRestoreUser = computed(() => page.props.can_restore_user);
const updatingStatusIds = ref<Record<string, boolean>>({});
const deleteForm = useForm({});

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

            <Button variant="outline" as-child v-show="canRestoreUser">
              <Link :href="showTeammateTrashPage.url(currentWorkspace.slug)">
                {{ t('回收站') }}
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
                  <th class="px-4 py-3">{{ t('邮箱') }}</th>
                  <th class="px-4 py-3">{{ t('身份') }}</th>
                  <th class="px-4 py-3">{{ t('在线状态') }}</th>
                  <th class="px-4 py-3 text-right">{{ t('操作') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="u in userList"
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
                          v-for="opt in onlineStatusOptions"
                          :key="String(opt.value)"
                          :value="String(opt.value)"
                        >
                          {{ opt.label }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
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
                            :disabled="!u.show_delete_button || deleteForm.processing"
                            :title="
                              !u.show_delete_button
                                ? t('当前登录用户不允许删除')
                                : undefined
                            "
                          >
                            {{ t('删除') }}
                          </Button>
                        </DialogTrigger>
                        <DialogContent>
                          <DialogHeader class="space-y-3">
                            <DialogTitle>
                              {{ t('确认删除客服？') }}
                            </DialogTitle>
                            <DialogDescription>
                              {{ t('将该客服账号放入回收站，可以后续恢复。') }}
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
                                :disabled="deleteForm.processing"
                              >
                                {{ t('取消') }}
                              </Button>
                            </DialogClose>
                            <Button
                              variant="destructive"
                              :disabled="
                                deleteForm.processing || !u.show_delete_button
                              "
                              @click="
                                deleteForm.delete(
                                  deleteTeammate.url({
                                    slug: currentWorkspace.slug,
                                    id: u.user_id,
                                  }),
                                  { preserveScroll: true },
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

                <tr v-if="userList.length === 0">
                  <td
                    class="px-4 py-8 text-center text-muted-foreground"
                    colspan="6"
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
