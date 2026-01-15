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
  deleteUser,
  showCreateUserPage,
  showEditUserPage,
  showUserList,
  showUserTrashPage,
  updateUserOnlineStatus,
} from '@/routes';
import type { AppPageProps, BreadcrumbItem } from '@/types';
import type {
  UserListPagePropsData,
  UserOnlineStatus,
  WorkspaceRole,
} from '@/types/generated';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { t } = useI18n();
const page = usePage<AppPageProps<UserListPagePropsData>>();
const currentWorkspace = useRequiredWorkspace();
const userList = computed(() => page.props.user_list);
const currentUserId = computed(() => String(page.props.auth?.user?.id ?? ''));

const updatingStatusIds = ref<Record<string, boolean>>({});
const deleteForm = useForm({});

const roleLabel = (role: WorkspaceRole) =>
  ({
    owner: t('所有者'),
    admin: t('管理员'),
    customer_service: t('客服'),
  })[role];

const onlineStatusLabel = (s: UserOnlineStatus) =>
  ({
    1: t('在线'),
    0: t('离线'),
  })[s];

const onlineStatusOptions = computed<
  { value: UserOnlineStatus; label: string }[]
>(() => [
  { value: 1, label: t('在线') },
  { value: 0, label: t('离线') },
]);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('多客服'),
    href: showUserList.url(currentWorkspace.value.slug),
  },
]);

const cannotDelete = (u: (typeof userList.value)[number]) =>
  String(u.id) === currentUserId.value;

const handleOnlineStatusChange = (userId: string, status: string) => {
  updatingStatusIds.value[userId] = true;
  router.put(
    updateUserOnlineStatus.url({
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
              <Link :href="showCreateUserPage.url(currentWorkspace.slug)">
                {{ t('新增客服') }}
              </Link>
            </Button>

            <Button variant="outline" as-child>
              <Link :href="showUserTrashPage.url(currentWorkspace.slug)">
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
                  :key="u.id"
                  class="border-t bg-background"
                >
                  <td class="px-4 py-3">
                    <Avatar class="h-9 w-9">
                      <AvatarImage v-if="u.avatar" :src="u.avatar" />
                      <AvatarFallback>
                        {{ (u.name || '').slice(0, 1) }}
                      </AvatarFallback>
                    </Avatar>
                  </td>
                  <td class="px-4 py-3 font-medium">
                    {{ u.name }}
                  </td>
                  <td class="px-4 py-3">
                    {{ u.email }}
                  </td>
                  <td class="px-4 py-3">
                    {{ roleLabel(u.role) }}
                  </td>
                  <td class="px-4 py-3">
                    <Select
                      :model-value="String(u.online_status)"
                      :disabled="updatingStatusIds[u.id]"
                      @update:model-value="
                        (v) => handleOnlineStatusChange(u.id, v)
                      "
                    >
                      <SelectTrigger class="h-9 w-28">
                        <SelectValue
                          :placeholder="onlineStatusLabel(u.online_status)"
                        >
                          {{ onlineStatusLabel(u.online_status) }}
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
                            showEditUserPage.url({
                              slug: currentWorkspace.slug,
                              id: u.id,
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
                            :disabled="cannotDelete(u) || deleteForm.processing"
                            :title="
                              cannotDelete(u)
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
                            <div class="font-medium">{{ u.name }}</div>
                            <div class="text-muted-foreground">
                              {{ u.email }}
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
                                deleteForm.processing || cannotDelete(u)
                              "
                              @click="
                                deleteForm.delete(
                                  deleteUser.url({
                                    slug: currentWorkspace.slug,
                                    id: u.id,
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
