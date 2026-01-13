import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezonePlugin from 'dayjs/plugin/timezone';
import 'dayjs/locale/zh-cn';
import 'dayjs/locale/en';

import { useI18n } from '@/composables/useI18n';
import { useTimezone } from '@/composables/useTimezone';

dayjs.extend(utc);
dayjs.extend(timezonePlugin);

const toDayjsLocale = (locale: string) => {
  if (locale.toLowerCase() === 'zh-cn') return 'zh-cn';
  if (locale.toLowerCase() === 'en') return 'en';
  return 'zh-cn';
};

export function useDateTime() {
  const { locale } = useI18n();
  const { timezone } = useTimezone();

  function formatDateTime(
    date: Date | string,
    format = 'YYYY-MM-DD HH:mm:ss',
  ): string {
    const d = typeof date === 'string' ? dayjs(date) : dayjs(date);
    return d
      .tz(timezone.value)
      .locale(toDayjsLocale(locale.value))
      .format(format);
  }

  return {
    formatDateTime,
  };
}

