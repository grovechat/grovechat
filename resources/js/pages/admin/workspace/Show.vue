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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { useDateTime } from '@/composables/useDateTime';
import { useI18n } from '@/composables/useI18n';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import {
  addWorkspaceMember,
  deleteWorkspaceMember,
  getWorkspaceList,
  showWorkspaceDetail,
} from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import type {
  FormAddWorkspaceMemberData,
  ShowWorkspaceDetailPagePropsData,
  WorkspaceRole,
} from '@/types/generated';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const { t } = useI18n();
const { formatDateTime } = useDateTime();
const props = defineProps<ShowWorkspaceDetailPagePropsData>();

const showAddDialog = ref(false);
const addForm = useForm<FormAddWorkspaceMemberData>({
  user_id: '',
  role: 'operator' as WorkspaceRole,
});
const deleteForm = useForm({});

watch(
  () => props.available_users,
  (opts) => {
    if (!addForm.user_id && opts?.length) {
      addForm.user_id = String(opts[0].id);
    }
  },
  { immediate: true },
);

watch(
  () => props.role_options,
  (opts) => {
    if (!addForm.role && opts?.length) {
      addForm.role = opts[0].value as WorkspaceRole;
    }
  },
  { immediate: true },
);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('工作区管理'),
    href: getWorkspaceList.url(),
  },
  {
    title: props.workspace?.name || t('详情'),
    href: showWorkspaceDetail.url(props.workspace?.id || ''),
  },
]);

const prevPage = computed(() =>
  Math.max(1, props.members.pagination.current_page - 1),
);
const nextPage = computed(() =>
  Math.min(
    props.members.pagination.last_page,
    props.members.pagination.current_page + 1,
  ),
);

const isOwner = (userId: string) =>
  !!props.workspace.owner?.id && String(props.workspace.owner.id) === String(userId);
</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('客服列表')" />
    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
        <div class="space-y-6">
          <div class="flex items-start justify-between gap-4">
            <HeadingSmall
              :title="props.workspace.name"
              :description="t('查看并管理该工作区的客服与管理员')"
            />
            <div class="flex items-center gap-2">
              <Dialog v-model:open="showAddDialog">
                <DialogTrigger as-child>
                  <Button :disabled="props.available_users.length === 0">
                    {{ t('添加客服') }}
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader class="space-y-3">
                    <DialogTitle>{{ t('添加客服') }}</DialogTitle>
                    <DialogDescription>
                      {{ t('选择用户并指定其身份为客服或管理员') }}
                    </DialogDescription>
                  </DialogHeader>

                  <div class="space-y-5">
                    <div class="grid gap-2">
                      <Label for="user_id">{{ t('用户') }}</Label>
                      <input type="hidden" name="user_id" :value="addForm.user_id" />
                      <Select v-model="addForm.user_id">
                        <SelectTrigger id="user_id" class="mt-1">
                          <SelectValue :placeholder="t('请选择用户')" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem
                            v-for="u in props.available_users"
                            :key="String(u.id)"
                            :value="String(u.id)"
                          >
                            {{ u.name }} ({{ u.email }})
                          </SelectItem>
                        </SelectContent>
                      </Select>
                      <p
                        v-if="props.available_users.length === 0"
                        class="text-sm text-muted-foreground"
                      >
                        {{ t('暂无可添加的用户') }}
                      </p>
                      <p v-if="addForm.errors.user_id" class="text-sm text-destructive">
                        {{ addForm.errors.user_id }}
                      </p>
                    </div>

                    <div class="grid gap-2">
                      <Label for="role">{{ t('身份') }}</Label>
                      <input type="hidden" name="role" :value="addForm.role" />
                      <Select v-model="addForm.role">
                        <SelectTrigger id="role" class="mt-1">
                          <SelectValue :placeholder="t('请选择身份')" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem
                            v-for="opt in props.role_options"
                            :key="String(opt.value)"
                            :value="String(opt.value)"
                          >
                            {{ opt.label }}
                          </SelectItem>
                        </SelectContent>
                      </Select>
                      <p v-if="addForm.errors.role" class="text-sm text-destructive">
                        {{ addForm.errors.role }}
                      </p>
                    </div>
                  </div>

                  <DialogFooter class="gap-2">
                    <DialogClose as-child>
                      <Button variant="secondary" :disabled="addForm.processing">
                        {{ t('取消') }}
                      </Button>
                    </DialogClose>
                    <Button
                      :disabled="addForm.processing || !addForm.user_id || !addForm.role"
                      @click="
                        addForm.post(addWorkspaceMember.url(props.workspace.id), {
                          preserveScroll: true,
                          onSuccess: () => {
                            showAddDialog = false;
                            addForm.reset();
                            addForm.clearErrors();
                          },
                        })
                      "
                    >
                      {{ addForm.processing ? t('添加中...') : t('确认添加') }}
                    </Button>
                  </DialogFooter>
                </DialogContent>
              </Dialog>
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
                    <th class="px-4 py-3 text-right font-medium">
                      {{ t('操作') }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="m in props.members.items"
                    :key="m.id"
                    class="border-b last:border-b-0"
                  >
                    <td class="px-4 py-3 font-medium">
                      {{ m.name }}
                      <span
                        v-if="m.deleted_at"
                        class="ml-2 text-xs text-muted-foreground"
                      >
                        {{ t('已删除') }}
                      </span>
                      <span
                        v-if="isOwner(m.id)"
                        class="ml-2 rounded bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                      >
                        {{ t('所有者') }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-muted-foreground">
                      {{ m.email }}
                    </td>
                    <td class="px-4 py-3">{{ m.role?.label || '-' }}</td>
                    <td class="px-4 py-3 text-muted-foreground">
                      {{ m.joined_at ? formatDateTime(m.joined_at) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                      <Dialog v-if="!isOwner(m.id)">
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
                            <DialogTitle>{{ t('确认删除成员？') }}</DialogTitle>
                            <DialogDescription>
                              {{ t('将从该工作区移除该成员的访问权限。') }}
                            </DialogDescription>
                          </DialogHeader>

                          <div class="rounded-md bg-muted/30 p-3 text-sm">
                            <div class="font-medium">{{ m.name }}</div>
                            <div class="text-muted-foreground">{{ m.email }}</div>
                          </div>

                          <DialogFooter class="gap-2">
                            <DialogClose as-child>
                              <Button variant="secondary">
                                {{ t('取消') }}
                              </Button>
                            </DialogClose>
                            <Button
                              variant="destructive"
                              @click="
                                deleteForm.delete(
                                  deleteWorkspaceMember.url({
                                    id: props.workspace.id,
                                    userId: m.id,
                                  }),
                                  { preserveScroll: true },
                                )
                              "
                            >
                              {{ t('确认删除') }}
                            </Button>
                          </DialogFooter>
                        </DialogContent>
                      </Dialog>
                      <span v-else class="text-sm text-muted-foreground">-</span>
                    </td>
                  </tr>

                  <tr v-if="props.members.items.length === 0">
                    <td
                      colspan="5"
                      class="px-4 py-8 text-center text-muted-foreground"
                    >
                      {{ t('暂无成员') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex items-center justify-between gap-3 border-t p-4">
              <div class="text-sm text-muted-foreground">
                {{ t('第') }} {{ props.members.pagination.current_page }} /
                {{ props.members.pagination.last_page }} {{ t('页，共') }}
                {{ props.members.pagination.total }} {{ t('条') }}
              </div>
              <div class="flex items-center gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  :disabled="props.members.pagination.current_page <= 1"
                  as-child
                >
                  <Link
                    :href="
                      showWorkspaceDetail.url(props.workspace.id, {
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
                  :disabled="
                    props.members.pagination.current_page >=
                    props.members.pagination.last_page
                  "
                  as-child
                >
                  <Link
                    :href="
                      showWorkspaceDetail.url(props.workspace.id, {
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
