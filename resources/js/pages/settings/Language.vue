<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
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
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { availableLocales, type Locale } from '@/locales';
import { type BreadcrumbItem } from '@/types';

const { locale, updateLocale, t } = useI18n();
const { timezone, updateTimezone, getTimezones, getCurrentTimezoneInfo } =
  useTimezone();

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: computed(() => t('language.title')).value,
    href: '/settings/language',
  },
];

const currentLanguageLabel = computed(() => {
  const current = availableLocales.find((l) => l.value === locale.value);
  return current?.label || locale.value;
});

// 根据当前语言获取时区列表
const timezones = computed(() => getTimezones(locale.value));

// 根据当前语言获取当前时区标签
const currentTimezoneLabel = computed(
  () => getCurrentTimezoneInfo(locale.value).label,
);

function handleLanguageChange(value: string) {
  updateLocale(value as Locale);
}

function handleTimezoneChange(value: string) {
  updateTimezone(value as Timezone);
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head :title="t('language.title')" />

    <SettingsLayout>
      <div class="flex flex-col space-y-6">
        <!-- 语言设置 -->
        <HeadingSmall
          :title="t('language.heading')"
          :description="t('language.description')"
        />

        <div class="space-y-4">
          <div class="grid gap-2">
            <Label for="language-select">{{ t('language.select') }}</Label>
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
          :title="t('timezone.heading')"
          :description="t('timezone.description')"
        />

        <div class="space-y-4">
          <div class="grid gap-2">
            <Label for="timezone-select">{{ t('timezone.select') }}</Label>
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
                <SelectItem
                  v-for="tz in timezones"
                  :key="tz.value"
                  :value="tz.value"
                >
                  {{ tz.label }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
