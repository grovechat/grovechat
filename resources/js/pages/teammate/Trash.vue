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
import { useDateTime } from '@/composables/useDateTime';
import { useI18n } from '@/composables/useI18n';
import { useRequiredWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import WorkspaceSettingsLayout from '@/layouts/WorkspaceSettingsLayout.vue';
import { restoreTeammate, showTeammateList, showTeammateTrashPage } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import type { ShowTrashTeammatePagePropsData } from '@/types/generated';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const { formatDateTime } = useDateTime();
const props = defineProps<ShowTrashTeammatePagePropsData>();
const currentWorkspace = useRequiredWorkspace();
const restoreForm = useForm({});

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('多客服'),
    href: showTeammateList.url(currentWorkspace.value.slug),
  },
  {
    title: t('客服回收站'),
    href: showTeammateTrashPage.url(currentWorkspace.value.slug),
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('客服回收站')" />

    <WorkspaceSettingsLayout content-class="max-w-none">
      <div class="space-y-6">
        <HeadingSmall
          :title="t('客服回收站')"
          :description="t('查看已删除的客服并可恢复')"
        />

        <div class="rounded-lg border">
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="border-b bg-muted/30 text-muted-foreground">
                <tr>
                  <th class="px-4 py-3 text-left font-medium">
                    {{ t('头像') }}
                  </th>
                  <th class="px-4 py-3 text-left font-medium">
                    {{ t('名称') }}
                  </th>
                  <th class="px-4 py-3 text-left font-medium">
                    {{ t('邮箱') }}
                  </th>
                  <th class="px-4 py-3 text-left font-medium">
                    {{ t('身份') }}
                  </th>
                  <th class="px-4 py-3 text-left font-medium">
                    {{ t('删除时间') }}
                  </th>
                  <th class="px-4 py-3 text-right font-medium">
                    {{ t('操作') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="u in props.user_list"
                  :key="u.id"
                  class="border-b last:border-b-0"
                >
                  <td class="px-4 py-3">
                    <Avatar class="h-9 w-9">
                      <AvatarImage v-if="u.avatar" :src="u.avatar" />
                      <AvatarFallback>
                        {{ (u.name || '').slice(0, 1) }}
                      </AvatarFallback>
                    </Avatar>
                  </td>
                  <td class="px-4 py-3">
                    <div class="font-medium">{{ u.name }}</div>
                  </td>
                  <td class="px-4 py-3 text-muted-foreground">{{ u.email }}</td>
                  <td class="px-4 py-3">
                    {{
                      t(
                        u.role === 'admin'
                          ? '管理员'
                          : u.role === 'owner'
                            ? '所有者'
                            : '客服',
                      )
                    }}
                  </td>
                  <td class="px-4 py-3 text-muted-foreground">
                    {{ u.deleted_at ? formatDateTime(u.deleted_at) : '-' }}
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
                            {{ t('确认恢复客服？') }}
                          </DialogTitle>
                          <DialogDescription>
                            {{ t('恢复后将重新出现在客服列表中。') }}
                          </DialogDescription>
                        </DialogHeader>

                        <div class="rounded-md bg-muted/30 p-3 text-sm">
                          <div class="font-medium">{{ u.name }}</div>
                          <div class="text-muted-foreground">{{ u.email }}</div>
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
                              restoreForm.put(
                                restoreTeammate.url({
                                  slug: currentWorkspace.slug,
                                  id: u.id,
                                }),
                                { preserveScroll: true },
                              )
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

                <tr v-if="props.user_list.length === 0">
                  <td
                    colspan="6"
                    class="px-4 py-8 text-center text-muted-foreground"
                  >
                    {{ t('暂无已删除的客服') }}
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
