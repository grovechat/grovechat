<script setup lang="ts">
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
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
import { useI18n } from '@/composables/useI18n';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import admin from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';
import type { ShowUserListPagePropsData } from '@/types/generated';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
const props = defineProps<ShowUserListPagePropsData>();
const resetTwoFactorForm = useForm({});

const prevPage = computed(() =>
  Math.max(1, props.user_list_pagination.current_page - 1),
);
const nextPage = computed(() =>
  Math.min(
    props.user_list_pagination.last_page,
    props.user_list_pagination.current_page + 1,
  ),
);

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('用户管理'),
    href: admin.getUserList.url(),
  },
]);
</script>

<template>
  <SystemAppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('用户管理')" />

    <div class="px-4 py-6 sm:px-6">
      <div class="mx-auto w-full max-w-none space-y-12">
        <div class="space-y-6">
          <div class="flex items-start justify-between gap-4">
            <HeadingSmall :title="t('用户管理')"/>

            <Button as-child>
              <Link :href="admin.showCreateUserPage.url()">
                {{ t('新增用户') }}
              </Link>
            </Button>
          </div>

          <div class="rounded-lg border">
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="border-b bg-muted/30 text-muted-foreground">
                  <tr class="text-left">
                    <th class="px-4 py-3">{{ t('头像') }}</th>
                    <th class="px-4 py-3">{{ t('名称') }}</th>
                    <th class="px-4 py-3">{{ t('邮箱') }}</th>
                    <th class="px-4 py-3">{{ t('两步验证') }}</th>
                    <th class="px-4 py-3 text-right">{{ t('操作') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="u in props.user_list"
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
                      <Badge
                        v-if="u.two_factor_enabled"
                        class="border-transparent bg-emerald-600 text-white hover:bg-emerald-600/90 dark:bg-emerald-500 dark:hover:bg-emerald-500/90"
                      >
                        {{ t('已启用') }}
                      </Badge>
                      <Badge
                        v-else
                        class="border-transparent bg-muted text-muted-foreground hover:bg-muted/80"
                      >
                        {{ t('未启用') }}
                      </Badge>
                    </td>
                    <td class="px-4 py-3 text-right">
                      <div class="inline-flex items-center gap-2">
                        <Button as-child variant="outline" size="sm">
                          <Link :href="admin.showEditUserPage.url(u.id)">
                            {{ t('编辑') }}
                          </Link>
                        </Button>

                        <Dialog v-if="u.two_factor_enabled">
                          <DialogTrigger as-child>
                            <Button variant="outline" size="sm">
                              {{ t('重置两步验证') }}
                            </Button>
                          </DialogTrigger>
                          <DialogContent>
                            <DialogHeader class="space-y-3">
                              <DialogTitle>{{ t('确认重置两步验证？') }}</DialogTitle>
                              <DialogDescription>
                                {{ t('重置后，该用户需要重新绑定两步验证。') }}
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
                                  :disabled="resetTwoFactorForm.processing"
                                >
                                  {{ t('取消') }}
                                </Button>
                              </DialogClose>
                              <Button
                                variant="destructive"
                                :disabled="resetTwoFactorForm.processing"
                                @click="
                                  resetTwoFactorForm.put(
                                    admin.resetUserTwoFactorAuthentication.url(u.id),
                                    { preserveScroll: true },
                                  )
                                "
                              >
                                {{
                                  resetTwoFactorForm.processing
                                    ? t('重置中...')
                                    : t('确认')
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
                      colspan="5"
                    >
                      {{ t('暂无用户') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex items-center justify-between gap-3 border-t p-4">
              <div class="text-sm text-muted-foreground">
                {{ t('第') }} {{ props.user_list_pagination.current_page }} /
                {{ props.user_list_pagination.last_page }} {{ t('页，共') }}
                {{ props.user_list_pagination.total }} {{ t('人') }}
              </div>
              <div class="flex items-center gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  :disabled="props.user_list_pagination.current_page <= 1"
                  as-child
                >
                  <Link
                    :href="admin.getUserList.url({ query: { page: prevPage } })"
                  >
                    {{ t('上一页') }}
                  </Link>
                </Button>
                <Button
                  variant="outline"
                  size="sm"
                  :disabled="
                    props.user_list_pagination.current_page >=
                    props.user_list_pagination.last_page
                  "
                  as-child
                >
                  <Link
                    :href="admin.getUserList.url({ query: { page: nextPage } })"
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

