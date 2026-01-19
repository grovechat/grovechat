<script setup lang="ts">
import HeadingSmall from '@/components/common/HeadingSmall.vue';
import InputError from '@/components/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
import { showTeammateList, updateTeammate } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import type { ShowEditTeammatePagePropsData } from '@/types/generated';
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { t } = useI18n();
const props = defineProps<ShowEditTeammatePagePropsData>();
const currentWorkspace = useRequiredWorkspace();

const roleValue = ref<string>(String(props.user_form.role ?? ''));

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('多客服'),
    href: showTeammateList.url(currentWorkspace.value.slug),
  },
  {
    title: t('编辑客服'),
    href: '#',
  },
]);
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('编辑客服')" />

    <WorkspaceSettingsLayout>
      <div class="space-y-6">
        <HeadingSmall
          :title="t('编辑客服')"
          :description="t('仅支持调整客服身份')"
        />

        <Form
          v-bind="
            updateTeammate.form({
              slug: currentWorkspace.slug,
              id: props.user_form.id,
            })
          "
          class="space-y-6"
          v-slot="{ errors, processing, recentlySuccessful }"
        >
          <div class="grid gap-2">
            <Label for="name">{{ t('客服名称') }}</Label>
            <Input
              id="name"
              class="mt-1 block w-full"
              disabled
              :default-value="props.user_form.name || ''"
            />
          </div>

          <div class="grid gap-2">
            <Label for="email">{{ t('邮箱') }}</Label>
            <Input
              id="email"
              type="email"
              class="mt-1 block w-full"
              disabled
              :default-value="props.user_form.email || ''"
            />
          </div>

          <div class="grid gap-2">
            <Label for="nickname">{{ t('对外昵称') }}</Label>
            <template v-if="props.can_update_nickname">
              <Input
                id="nickname"
                name="nickname"
                class="mt-1 block w-full"
                :default-value="props.user_form.nickname || ''"
                :placeholder="t('请输入对外昵称')"
              />
            </template>
            <template v-else>
              <input
                type="hidden"
                name="nickname"
                :value="props.user_form.nickname || ''"
              />
              <Input
                id="nickname"
                class="mt-1 block w-full"
                disabled
                :default-value="props.user_form.nickname || ''"
              />
            </template>
            <InputError class="mt-2" :message="errors.nickname" />
          </div>

          <div class="grid gap-2">
            <Label for="role">{{ t('身份') }}</Label>
            <template v-if="props.can_update_role">
              <input type="hidden" name="role" :value="roleValue" />
              <Select v-model="roleValue">
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
            </template>
            <template v-else>
              <input
                type="hidden"
                name="role"
                :value="String(props.user_form.role)"
              />
              <Input
                id="role"
                class="mt-1"
                :default-value="props.user_form.role_label"
                disabled
              />
            </template>
            <InputError class="mt-2" :message="errors.role" />
          </div>

          <div class="flex items-center gap-4">
            <Button type="submit" :disabled="processing">
              {{ t('保存') }}
            </Button>

            <Transition
              enter-active-class="transition ease-in-out"
              enter-from-class="opacity-0"
              leave-active-class="transition ease-in-out"
              leave-to-class="opacity-0"
            >
              <p v-show="recentlySuccessful" class="text-sm text-neutral-600">
                {{ t('保存成功。') }}
              </p>
            </Transition>
          </div>
        </Form>
      </div>
    </WorkspaceSettingsLayout>
  </AppLayout>
</template>
