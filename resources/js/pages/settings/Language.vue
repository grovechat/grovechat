<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import HeadingSmall from '@/components/common/HeadingSmall.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { useI18n } from '@/composables/useI18n';
import { useTimezone, type Timezone } from '@/composables/useTimezone';
import { useCurrentWorkspace } from '@/composables/useWorkspace';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import SystemAppLayout from '@/layouts/SystemAppLayout.vue';
import { availableLocales, type Locale } from '@/locales';
import { edit } from '@/routes/language';
import { type BreadcrumbItem } from '@/types';

const { locale, updateLocale, t } = useI18n();
const { timezone, updateTimezone, getTimezones, getCurrentTimezoneInfo } =
  useTimezone();

const page = usePage();
const currentWorkspace = useCurrentWorkspace();
const RootLayout = computed(() =>
  page.props.auth.user.is_super_admin ? SystemAppLayout : AppLayout,
);
const linkOptions = computed(() => ({
  mergeQuery: {
    from_workspace: currentWorkspace.value?.slug ?? '',
  },
}));

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
  {
    title: t('语言和时区设置'),
    href: edit.url(linkOptions.value),
  },
]);

const currentLanguageLabel = computed(() => {
  const current = availableLocales.find((l) => l.value === locale.value);
  return current?.label || locale.value;
});

// 根据当前语言获取时区列表
const timezoneSearch = ref('');
const timezones = computed(() => {
  const list = getTimezones(locale.value);
  const q = timezoneSearch.value.trim().toLowerCase();
  if (!q) return list;
  return list.filter((tz) => {
    const hay = `${tz.label} ${tz.value} ${tz.offset}`.toLowerCase();
    return hay.includes(q);
  });
});

// 根据当前语言获取当前时区标签
const currentTimezoneLabel = computed(
  () => getCurrentTimezoneInfo(locale.value).label,
);

function handleLanguageChange(value: any) {
  if (typeof value !== 'string') return;
  updateLocale(value as Locale);
}

function handleTimezoneChange(value: any) {
  if (typeof value !== 'string') return;
  updateTimezone(value as Timezone);
}
</script>

<template>
  <component :is="RootLayout" :breadcrumbs="breadcrumbItems">
    <Head :title="t('语言和时区设置')" />

    <SettingsLayout>
      <div class="flex flex-col space-y-6">
        <!-- 语言设置 -->
        <HeadingSmall
          :title="t('语言偏好')"
          :description="t('选择你的首选语言')"
        />

        <div class="space-y-4">
          <div class="grid gap-2">
            <Label for="language-select">{{ t('选择语言') }}</Label>
            <Select
              :model-value="locale"
              @update:model-value="handleLanguageChange"
            >
              <SelectTrigger id="language-select">
                <SelectValue :placeholder="currentLanguageLabel">
                  {{ currentLanguageLabel }}
                </SelectValue>
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="lang in availableLocales"
                  :key="lang.value"
                  :value="lang.value"
                >
                  {{ lang.label }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        <Separator class="my-6" />

        <!-- 时区设置 -->
        <HeadingSmall
          :title="t('时区设置')"
          :description="t('选择你的时区，用于正确显示时间')"
        />

        <div class="space-y-4">
          <div class="grid gap-2">
            <Label for="timezone-select">{{ t('选择时区') }}</Label>
            <Select
              :model-value="timezone"
              @update:model-value="handleTimezoneChange"
            >
              <SelectTrigger id="timezone-select">
                <SelectValue :placeholder="currentTimezoneLabel">
                  {{ currentTimezoneLabel }}
                </SelectValue>
              </SelectTrigger>
              <SelectContent>
                <template #header>
                  <div class="border-b bg-popover p-2">
                    <Input
                      v-model="timezoneSearch"
                      :placeholder="t('搜索时区（名称 / IANA / UTC 偏移）')"
                      @keydown.stop
                    />
                  </div>
                </template>
                <SelectItem
                  v-for="tz in timezones"
                  :key="tz.value"
                  :value="tz.value"
                >
                  {{ tz.label }}
                </SelectItem>
                <div
                  v-if="timezones.length === 0"
                  class="px-3 py-2 text-sm text-muted-foreground"
                >
                  {{ t('未找到匹配的时区') }}
                </div>
              </SelectContent>
            </Select>
          </div>
        </div>
      </div>
    </SettingsLayout>
  </component>
</template>
